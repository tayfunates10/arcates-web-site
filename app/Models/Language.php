<?php
/**
 * Diller.
 *
 * Varsayilan dil oneksizdir; Arapca `rtl` yondedir.  DOCS.md 4.6, 11.3
 */

declare(strict_types=1);

namespace Arcates\Models;

final class Language extends Model
{
    protected static string $table = 'languages';

    public static function all(): array
    {
        return self::db()->all('SELECT * FROM languages ORDER BY sort, code');
    }

    public static function active(): array
    {
        return self::db()->all('SELECT * FROM languages WHERE is_active = 1 ORDER BY sort, code');
    }

    public static function byCode(string $code): ?array
    {
        return self::db()->first('SELECT * FROM languages WHERE code = :code', [':code' => $code]);
    }

    /** Yalnizca bir dil varsayilan olabilir. */
    public static function setDefault(string $code): void
    {
        self::db()->transaction(static function ($db) use ($code): void {
            $db->run('UPDATE languages SET is_default = 0');
            $db->update('languages', ['is_default' => 1, 'is_active' => 1], ['code' => $code]);
        });
    }
}
