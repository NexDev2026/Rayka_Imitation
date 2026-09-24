<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(
        protected string $apiKey,
        protected ?string $defaultFromEmail = null,
        protected ?string $defaultFromName = null,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $senderAddress = $email->getFrom()[0] ?? new Address($this->defaultFromEmail ?: 'noreply@lynkova.in', $this->defaultFromName ?: 'Rayka');
        $senderEmail = $senderAddress->getAddress();
        $senderName = $senderAddress->getName() ?: ($this->defaultFromName ?: 'Rayka Imitation Jewellery');

        // Safety check: Brevo rejects @gmail.com or @yahoo.com as unauthenticated sender domains
        if (str_contains(strtolower($senderEmail), '@gmail.com') || str_contains(strtolower($senderEmail), '@yahoo.com')) {
            $senderEmail = $this->defaultFromEmail ?: 'noreply@lynkova.in';
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
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 201) {
            Log::error("Brevo API Mail dispatch failed [HTTP {$httpCode}]: {$response} | cURL error: {$curlError}");
            if ($httpCode === 0) {
                throw new \RuntimeException("Brevo API Connection failed: {$curlError}");
            }
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
