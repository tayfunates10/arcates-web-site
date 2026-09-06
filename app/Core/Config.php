<?php
/**
 * Yapilandirma erisimi.
 *
 * `config/config.php` bir dizi dondurur; bu sinif noktali anahtarla okuma
 * saglar: Config::get('db.host').  DOCS.md 13
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Config
{
    private static array $data = [];
    private static bool $loaded = false;

    /** Yapilandirmayi dosyadan veya verilen diziden yukler. */
    public static function load(?array $data = null): void
    {
        if ($data !== null) {
            self::$data   = $data;
            self::$loaded = true;
            return;
        }

        $file = ARC_ROOT . '/config/config.php';
        if (!is_file($file)) {
            $file = ARC_ROOT . '/config/config.example.php';
        }

        $loaded = require $file;
        if (!is_array($loaded)) {
            throw new \RuntimeException('Yapilandirma dosyasi dizi dondurmeli.');
        }

        self::$data   = $loaded;
        self::$loaded = true;
    }

    /** Kurulum yapilmis mi? `config/config.php` var mi diye bakar. */
    public static function exists(): bool
    {
        return is_file(ARC_ROOT . '/config/config.php');
    }

    /**
     * Noktali anahtarla deger okur.
     *
     * @param string $key     Ornegin 'app.base_url'.
     * @param mixed  $default Anahtar yoksa donecek deger.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::load();
        }

        $value = self::$data;
        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /** Yalnizca calisma zamaninda gecerli olan gecici deger atar. */
    public static function set(string $key, mixed $value): void
    {
        if (!self::$loaded) {
            self::load();
        }

        $segments = explode('.', $key);
        $ref      = &self::$data;
        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $ref[$segment] = $value;
                break;
            }
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }
    }

    /** Tum yapilandirmayi dondurur (testler icin). */
    public static function all(): array
    {
        if (!self::$loaded) {
            self::load();
        }
        return self::$data;
    }
}
