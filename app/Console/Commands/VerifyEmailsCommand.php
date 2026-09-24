<?php

namespace App\Console\Commands;

use App\Mail\AdminNewRegistrationMail;
use App\Mail\DatabaseBackupMail;
use App\Mail\LoginOtpMail;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderCancelledByAdminCustomerMail;
use App\Mail\OrderCancelledByCustomerAdminMail;
use App\Mail\OrderCancelledCustomerMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Mail\OrderStatusUpdatedCustomerMail;
use App\Mail\PasswordChangedAlertMail;
use App\Mail\RegisterOtpMail;
use App\Mail\ResetPasswordOtpMail;
use App\Mail\VerifyNewEmailOtpMail;
use App\Mail\WelcomeUserMail;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Console\Command;

class VerifyEmailsCommand extends Command
{
    protected $signature = 'emails:verify';

    protected $description = 'Render and validate all Mailable classes using fake model stubs (no emails sent).';

    public function handle(): int
    {
        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('  Rayka — Email System Verification Suite');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $user = new User([
            'id' => 1,
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'mobile' => '9876543210',
        ]);

        $address = new Address([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'mobile' => '9876543210',
            'address_line' => '101, Royal Heights Apartment (Home)',
            'street' => 'MG Road',
            'city' => 'Surat',
            'state' => 'Gujarat',
            'pincode' => '395003',
            'landmark' => 'Near City Mall',
        ]);

        $order = (new Order)->forceFill([
            'id' => 1001,
            'order_number' => 'RAY-20260922-TEST1',
            'subtotal' => 2499.00,
            'coupon_discount' => 200.00,
            'coupon_code' => 'ROYAL20',
            'shipping_fee' => 0.00,
            'total_amount' => 2299.00,
            'status' => 'Confirmed',
            'notes' => 'Please pack carefully.',
            'created_at' => now(),
        ]);
        $order->exists = true;
        $order->setRelation('user', $user);
        $order->setRelation('address', $address);
        $order->setRelation('payment', null);


        $item = new OrderItem([
            'product_name' => 'Gold Plated Kundan Necklace Set',
            'product_sku' => 'KND-001',
            'variant_info' => 'Gold',
            'unit_price' => 1249.50,
            'quantity' => 2,
            'subtotal' => 2499.00,
        ]);
        $order->setRelation('items', collect([$item]));

        $backupResult = [
            'status' => 'success',
            'filename' => 'rayka_backup_test.sql.gz',
            'size_readable' => '1.24 MB',
            'tables' => 18,
            'rows' => 3842,
            'duration_sec' => 2.31,
            'rotated' => 0,
        ];

        $mailables = [
            '1.  LoginOtpMail'                                => fn () => new LoginOtpMail('482930'),
            '2.  RegisterOtpMail'                             => fn () => new RegisterOtpMail('736201'),
            '3.  ResetPasswordOtpMail'                        => fn () => new ResetPasswordOtpMail('918274'),
            '4.  VerifyNewEmailOtpMail'                       => fn () => new VerifyNewEmailOtpMail('654321'),
            '5.  WelcomeUserMail'                             => fn () => new WelcomeUserMail($user),
            '6.  PasswordChangedAlertMail'                    => fn () => new PasswordChangedAlertMail($user),
            '7.  AdminNewRegistrationMail'                    => fn () => new AdminNewRegistrationMail($user),
            '8.  OrderPlacedCustomerMail'                     => fn () => new OrderPlacedCustomerMail($order),
            '9.  NewOrderAdminMail'                           => fn () => new NewOrderAdminMail($order),
            '10. OrderStatusUpdatedCustomerMail (Shipped)'   => fn () => new OrderStatusUpdatedCustomerMail($order, 'Shipped', 'SHIP-789-RAY'),
            '11. OrderStatusUpdatedCustomerMail (Confirmed)' => fn () => new OrderStatusUpdatedCustomerMail($order, 'Confirmed', null),
            '12. OrderStatusUpdatedCustomerMail (Delivered)' => fn () => new OrderStatusUpdatedCustomerMail($order, 'Delivered', null),
            '13. OrderCancelledByAdminCustomerMail (Cancel)' => fn () => new OrderCancelledByAdminCustomerMail($order, 'Cancelled', 'Payment screenshot did not match.'),
            '14. OrderCancelledByAdminCustomerMail (Reject)'  => fn () => new OrderCancelledByAdminCustomerMail($order, 'Rejected', 'UPI screenshot blurry and amount mismatch.'),
            '15. OrderCancelledCustomerMail'                  => fn () => new OrderCancelledCustomerMail($order, 'Customer requested cancellation'),
            '16. OrderCancelledByCustomerAdminMail'           => fn () => new OrderCancelledByCustomerAdminMail($order, 'Customer request', 'Received a better deal elsewhere'),
            '17. DatabaseBackupMail'                          => fn () => new DatabaseBackupMail($backupResult),
        ];

        $pass = 0;
        $fail = 0;

        foreach ($mailables as $label => $factory) {
            try {
                /** @var \Illuminate\Mail\Mailable $mailable */
                $mailable = $factory();
                $content = $mailable->content();
                $viewData = $content->with ?? [];

                // Merge common model stubs so views that use $order / $user directly work
                $viewData = array_merge(['order' => $order, 'user' => $user], $viewData);

                $rendered = view($content->view, $viewData)->render();

                if (strlen($rendered) < 200) {
                    throw new \RuntimeException('Rendered HTML is suspiciously short ('.strlen($rendered).' chars). Template may have a conditional short-circuit.');
                }

                $this->line("  <fg=green>✔</> {$label} <fg=gray>(" . number_format(strlen($rendered)) . ' chars)</>');
                $pass++;
            } catch (\Throwable $e) {
                $this->line("  <fg=red>✘</> {$label}");
                $this->line("    <fg=red>└─ " . $e->getMessage() . '</>');
                $fail++;
            }
        }

        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        if ($fail === 0) {
            $this->info("  ✅  All {$pass} mailables rendered successfully.");
        } else {
            $this->warn("  ⚠️  {$pass} passed / {$fail} failed — see errors above.");
        }
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('');

        return $fail === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
