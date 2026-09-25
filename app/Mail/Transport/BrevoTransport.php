<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(
        protected string $apiKey,
        protected ?string $defaultFromEmail = null,
        protected ?string $defaultFromName = null,
    ) {
        parent::__construct();
        if (empty($this->apiKey)) {
            $this->apiKey = (string) (config('services.brevo.key') ?: env('BREVO_API_KEY', ''));
        }
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $defaultVerified = $this->defaultFromEmail ?: (string) config('services.brevo.from_email', 'noreply@lynkova.in');
        $senderAddress = $email->getFrom()[0] ?? new Address($defaultVerified, $this->defaultFromName ?: 'Rayka');
        $senderEmail = $senderAddress->getAddress();
        $senderName = $senderAddress->getName() ?: ($this->defaultFromName ?: 'Rayka Imitation Jewellery');

        // Always enforce verified sender domain for Brevo API acceptance
        if (empty($senderEmail) || ! str_ends_with(strtolower($senderEmail), 'lynkova.in')) {
            $senderEmail = $defaultVerified;
        }

        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: $address->getAddress(),
            ];
        }

        $cc = [];
        foreach ($email->getCc() as $address) {
            $cc[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: $address->getAddress(),
            ];
        }

        $bcc = [];
        foreach ($email->getBcc() as $address) {
            $bcc[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: $address->getAddress(),
            ];
        }

        $htmlContent = $email->getHtmlBody();
        $textContent = $email->getTextBody() ?: strip_tags((string) $htmlContent);

        $payload = [
            'sender' => [
                'email' => $senderEmail,
                'name' => $senderName,
            ],
            'to' => $to,
            'replyTo' => [
                'email' => (string) config('services.brevo.admin_email', 'nexdevstudio01@gmail.com'),
                'name' => 'Rayka Support',
            ],
            'subject' => $email->getSubject() ?: 'Notification',
            'htmlContent' => (string) $htmlContent,
            'textContent' => (string) $textContent,
        ];

        if (! empty($cc)) {
            $payload['cc'] = $cc;
        }

        if (! empty($bcc)) {
            $payload['bcc'] = $bcc;
        }

        // Handle attachments (e.g. PDF invoices)
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $filename = $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename')
                ?: ($attachment->getPreparedHeaders()->getHeaderParameter('Content-Type', 'name') ?: 'document.pdf');

            $attachments[] = [
                'name' => $filename,
                'content' => base64_encode($attachment->getBody()),
            ];
        }

        if (! empty($attachments)) {
            $payload['attachment'] = $attachments;
        }

        $response = null;
        $httpCode = 0;
        $curlError = '';

        if (function_exists('curl_init')) {
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'api-key: ' . $this->apiKey,
                    'content-type: application/json',
                ],
                CURLOPT_TIMEOUT => 25,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = (string) curl_error($ch);
            curl_close($ch);
        }

        // Fallback to stream context / file_get_contents if curl is missing or failed to connect
        if ($httpCode === 0) {
            try {
                $opts = [
                    'http' => [
                        'method' => 'POST',
                        'header' => "accept: application/json\r\napi-key: {$this->apiKey}\r\ncontent-type: application/json\r\n",
                        'content' => json_encode($payload),
                        'timeout' => 25,
                        'ignore_errors' => true,
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ];
                $context = stream_context_create($opts);
                $res = @file_get_contents('https://api.brevo.com/v3/smtp/email', false, $context);
                if ($res !== false) {
                    $response = $res;
                    if (isset($http_response_header) && preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0], $m)) {
                        $httpCode = (int) $m[1];
                    }
                }
            } catch (\Throwable $e) {
                // Keep original error
            }
        }

        if ($httpCode !== 201) {
            Log::error("Brevo API Mail dispatch failed [HTTP {$httpCode}]: {$response} | cURL error: {$curlError}");
            throw new \RuntimeException("Brevo API Mail dispatch failed [HTTP {$httpCode}]: {$response} " . ($curlError ? "| cURL error: {$curlError}" : ''));
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
