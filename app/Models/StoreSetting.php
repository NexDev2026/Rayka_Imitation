<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Retrieve all store settings as a key-value dictionary with caching and defaults.
     *
     * @return array<string, mixed>
     */
    public static function getAll(): array
    {
        return Cache::remember('store_settings_all', 86400, function () {
            $settings = static::pluck('value', 'key')->toArray();

            $defaults = [
                'store_name' => 'Rayka Imitation Jewellery',
                'tagline' => 'Royal Heritage & 1 Gram Micro Gold Imitation Jewellery',
                'store_phone' => '+91 9638868024',
                'store_whatsapp' => '+91 9638868024',
                'store_alt_phone' => '+91 93164 53838',
                'store_email' => 'raykaimitation@gmail.com',
                'admin_email' => 'nexdevstudio01@gmail.com',
                'store_address' => 'Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad - 380026, Gujarat',
                'upi_id' => 'raykajewellery@icici',
                'upi_payee_name' => 'Rayka Imitation Jewellery Pvt Ltd',
                'qr_code_image' => '/images/qr/sample-upi-qr.svg',
                'free_shipping_min' => '999',
                'shipping_flat_fee' => '99',
                'trust_badge_1' => 'Free Express Shipping',
                'trust_badge_2' => '100% Verified Payment',
                'trust_badge_3' => 'Easy Replacement',
                'trust_badge_4' => 'Heritage Quality',
                'showcase_enabled' => '1',
                'showcase_title' => 'Curated Royal Collections',
                'showcase_subtitle' => 'Select a collection below to discover hand-finished 1 gram micro gold masterpieces.',
                'showcase_limit' => '10',
                'showcase_categories' => '["1","2","9","3","10"]',
                'instagram_url' => 'https://www.instagram.com/rayka_imitation_amdavad/?hl=en',
                'instagram_handle' => '@rayka_imitation_amdavad',
                'google_map_url' => 'https://share.google/vaohJv28SH29hBV8j',
            ];

            $merged = array_merge($defaults, $settings);

            // Compute clean phone (only digits)
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($merged['store_phone'] ?? ''));
            if (strlen($cleanPhone) === 10) {
                $cleanPhone = '91'.$cleanPhone;
            }

            // Compute clean WhatsApp (only digits with 91 prefix)
            $rawWa = (string) ($merged['store_whatsapp'] ?? $merged['whatsapp_number'] ?? '');
            $cleanWa = preg_replace('/[^0-9]/', '', $rawWa);
            if (strlen($cleanWa) === 10) {
                $cleanWa = '91'.$cleanWa;
            } elseif (str_starts_with($cleanWa, '0') && strlen($cleanWa) === 11) {
                $cleanWa = '91'.substr($cleanWa, 1);
            }

            if (empty($cleanWa)) {
                $cleanWa = '919638868024';
            }

            $merged['clean_phone'] = $cleanPhone ?: '919638868024';
            $merged['clean_whatsapp'] = $cleanWa;
            $merged['whatsapp_url'] = 'https://wa.me/'.$cleanWa;

            return $merged;
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = static::getAll();

        // Handle common aliases transparently
        if ($key === 'whatsapp_number' || $key === 'whatsapp') {
            return $all['store_whatsapp'] ?? $all['whatsapp_number'] ?? $default;
        }
        if ($key === 'phone_number' || $key === 'phone') {
            return $all['store_phone'] ?? $all['phone_number'] ?? $default;
        }

        if (array_key_exists($key, $all)) {
            return $all[$key] !== null ? (string) $all[$key] : $default;
        }

        return $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('store_settings_all');
    }

    public static function clearCache(): void
    {
        Cache::forget('store_settings_all');
    }
}
