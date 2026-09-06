<?php
/**
 * Modeller icin ortak taban.
 *
 * Ceviri deseni: ana tablo dilden bagimsiz alanlari, `_translations` tablosu
 * dile bagli alanlari tutar.  DOCS.md 8
 */

declare(strict_types=1);

namespace Arcates\Models;

use Arcates\Core\Database;
use Arcates\Core\Security;

abstract class Model
{
    /** Ana tablo adi. */
    protected static string $table = '';

    /** Ceviri tablosu adi; yoksa bos. */
    protected static string $translations = '';

    /** Ceviri tablosundaki yabanci anahtar sutunu. */
    protected static string $foreignKey = '';

    protected static function db(): Database
    {
        return Database::instance();
    }

    public static function table(): string
    {
        return static::$table;
    }

    public static function find(int $id): ?array
    {
        return self::db()->first(
            'SELECT * FROM `' . Database::identifier(static::$table) . '` WHERE id = :id',
            [':id' => $id]
        );
    }

    /** Kaydin belirli dildeki cevirisi. */
    public static function translation(int $id, string $lang): ?array
    {
        if (static::$translations === '') {
            return null;
        }

        return self::db()->first(
            'SELECT * FROM `' . Database::identifier(static::$translations) . '`'
            . ' WHERE `' . Database::identifier(static::$foreignKey) . '` = :id AND lang = :lang',
            [':id' => $id, ':lang' => $lang]
        );
    }

    /**
     * Kaydin cevirisi olan dil kodlari.
     *
     * `hreflang` uretiminde kullanilir; karsiligi olmayan dil listeye girmez.
     * DOCS.md 11.3, test U-13
     */
    public static function translatedLanguages(int $id): array
    {
        if (static::$translations === '') {
            return [];
        }

        return self::db()->column(
            'SELECT lang FROM `' . Database::identifier(static::$translations) . '`'
            . ' WHERE `' . Database::identifier(static::$foreignKey) . '` = :id ORDER BY lang',
            [':id' => $id]
        );
    }

    /**
     * Cakismayan slug uretir. Ayni dilde ayni slug varsa `-2`, `-3` … eklenir.
     * DOCS.md 14.2 test U-06
     *
     * @param int|null $ignoreId Guncellenen kaydin kendi kimligi.
     */
    public static function uniqueSlug(string $slug, string $lang, ?int $ignoreId = null): string
    {
        if (static::$translations === '') {
            return $slug;
        }

        $slug = Security::slug($slug);
        if ($slug === '') {
            $slug = 'sayfa';
        }

        $table   = Database::identifier(static::$translations);
        $foreign = Database::identifier(static::$foreignKey);

        $candidate = $slug;
        $suffix    = 1;

        while (true) {
            $sql  = 'SELECT COUNT(*) FROM `' . $table . '` WHERE lang = :lang AND slug = :slug';
            $args = [':lang' => $lang, ':slug' => $candidate];

            if ($ignoreId !== null) {
                $sql .= ' AND `' . $foreign . '` <> :ignore';
                $args[':ignore'] = $ignoreId;
            }

            if ((int) self::db()->value($sql, $args) === 0) {
                return $candidate;
            }

            $suffix++;
            $candidate = $slug . '-' . $suffix;
        }
    }

    /** Slug'a gore ceviri kaydi. */
    public static function bySlug(string $slug, string $lang): ?array
    {
        if (static::$translations === '') {
            return null;
        }

        return self::db()->first(
            'SELECT * FROM `' . Database::identifier(static::$translations) . '` WHERE lang = :lang AND slug = :slug',
            [':lang' => $lang, ':slug' => $slug]
        );
    }
}
