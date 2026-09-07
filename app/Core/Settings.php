<?php
/**
 * `settings` tablosu icin anahtar/deger erisimi.
 *
 * `autoload = 1` olan kayitlar tek sorguda okunur ve istek boyunca onbellekte
 * tutulur.  DOCS.md 8.1, 9.11
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Settings
{
    private static ?array $cache = null;

    /** Otomatik yuklenen ayarlari tek sorguda okur. */
    private static function boot(): void
    {
        if (self::$cache !== null) {
            return;
        }

        self::$cache = [];
        try {
            foreach (Database::instance()->all('SELECT `key`, `value` FROM settings WHERE autoload = 1') as $row) {
                self::$cache[$row['key']] = $row['value'];
            }
        } catch (\Throwable $e) {
            Logger::exception($e);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::boot();

        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        try {
            $value = Database::instance()->value('SELECT `value` FROM settings WHERE `key` = :key', [':key' => $key]);
        } catch (\Throwable) {
            return $default;
        }

        if ($value === null) {
            return $default;
        }

        self::$cache[$key] = $value;
        return $value;
    }

    /** JSON olarak saklanan ayari dizi olarak dondurur. */
    public static function getArray(string $key, array $default = []): array
    {
        $raw = self::get($key);
        if (!is_string($raw) || $raw === '') {
            return $default;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $raw = self::get($key);
        if ($raw === null) {
            return $default;
        }
        return in_array((string) $raw, ['1', 'true', 'on', 'evet'], true);
    }

    public static function getInt(string $key, int $default = 0): int
    {
        $raw = self::get($key);
        return is_numeric($raw) ? (int) $raw : $default;
    }

    public static function set(string $key, mixed $value, bool $autoload = true): void
    {
        if (is_array($value)) {
            $value = Security::json($value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif ($value !== null) {
            $value = (string) $value;
        }

        Database::instance()->upsert(
            'settings',
            ['key' => $key, 'value' => $value, 'autoload' => $autoload ? 1 : 0],
            ['value', 'autoload']
        );

        self::boot();
        self::$cache[$key] = $value;
    }

    /** Toplu yazim. */
    public static function setMany(array $pairs, bool $autoload = true): void
    {
        foreach ($pairs as $key => $value) {
            self::set((string) $key, $value, $autoload);
        }
    }

    public static function all(): array
    {
        self::boot();
        return self::$cache;
    }

    /** Onbellegi bosaltir (testler ve kurulum icin). */
    public static function flush(): void
    {
        self::$cache = null;
    }

    /**
     * Ilk kurulumda yazilan varsayilanlar. DOCS.md 9.11, 11.2
     *
     * NAP bilgileri Google Isletme Profili ile birebir ayni olmalidir; bu
     * degerler yalnizca baslangic noktasidir ve panelden duzenlenir.
     */
    public static function defaults(): array
    {
        return [
            'site_name'          => 'Arcates Yazılım',
            'site_tagline'       => 'Edremit Körfezi için web tasarım ve yazılım',
            'nap_name'           => 'Arcates Yazılım',
            'nap_street'         => 'Tuzcumurat Mah. 27016 Sk. Uysal Apt. No: 5 Kat: 3 Daire: 8',
            'nap_district'       => 'Edremit',
            'nap_city'           => 'Balıkesir',
            'nap_postcode'       => '',
            'nap_country'        => 'TR',
            'nap_phone'          => '+90 545 946 50 73',
            'nap_email'          => 'info@arcatesyazilim.com',
            'nap_lat'            => '39.5961',
            'nap_lng'            => '26.9361',
            'opening_hours'      => Security::json([
                ['days' => 'Mo-Fr', 'opens' => '09:00', 'closes' => '18:00'],
                ['days' => 'Sa', 'opens' => '10:00', 'closes' => '14:00'],
            ]),
            'social_links'       => Security::json([]),
            'meta_title_pattern' => '%title% | %site%',
            'meta_description'   => '',
            'robots_txt'         => '',
            'gsc_verification'   => '',
            'analytics_code'     => '',
            'maintenance_mode'   => '0',
            'maintenance_text'   => 'Sitemiz kısa süreliğine bakımda. Kısa süre sonra tekrar deneyin.',
            'default_lang'       => 'tr',
            'projects_notice'    => 'Buradaki siteler, yapabildiklerimizi göstermek için hazırlanmış örnek kurgulardır; henüz yayına alınmış müşteri işleri değildir.',
            'works_limit'        => '6',
            'submission_days'    => '730',
        ];
    }
}
