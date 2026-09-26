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
                'tagline' => '',
                'store_phone' => '',
                'store_whatsapp' => '',
                'store_alt_phone' => '',
                'store_email' => '',
                'admin_email' => '',
                'store_address' => '',
                'upi_id' => '',
                'upi_payee_name' => '',
                'qr_code_image' => '',
                'free_shipping_min' => '999',
                'shipping_flat_fee' => '99',
                'trust_badge_1' => '',
                'trust_badge_2' => '',
                'trust_badge_3' => '',
                'trust_badge_4' => '',
                'showcase_enabled' => '0',
                'showcase_title' => '',
                'showcase_subtitle' => '',
                'showcase_limit' => '10',
                'showcase_categories' => '[]',
                'instagram_url' => '',
                'instagram_handle' => '',
                'google_map_url' => '',
            ];

            // Start with defaults, then override with any existing keys in DB (including empty strings)
            $merged = array_merge($defaults, $settings);
            foreach ($merged as $k => $v) {
                if ($v === null) {
                    $merged[$k] = '';
                }
            }

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

            $merged['clean_phone'] = $cleanPhone;
            $merged['clean_whatsapp'] = $cleanWa;
            $merged['whatsapp_url'] = $cleanWa ? 'https://wa.me/'.$cleanWa : '';

            return $merged;
        });
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = static::getAll();

        // Handle common aliases transparently
        if ($key === 'whatsapp_number' || $key === 'whatsapp') {
            return array_key_exists('store_whatsapp', $all) ? (string) $all['store_whatsapp'] : ($all['whatsapp_number'] ?? $default);
        }
        if ($key === 'phone_number' || $key === 'phone') {
            return array_key_exists('store_phone', $all) ? (string) $all['store_phone'] : ($all['phone_number'] ?? $default);
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
            ['value' => $value !== null ? trim((string) $value) : '']
        );

        Cache::forget('store_settings_all');
    }

    public static function clearCache(): void
    {
        Cache::forget('store_settings_all');
    }
}
