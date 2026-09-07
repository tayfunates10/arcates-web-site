<?php
/**
 * Sema goc uygulayicisi.
 *
 * `db/schema.sql` yalnizca sifirdan kurulum icindir. Her sema degisikligi
 * `db/migrations/YYYY_MM_DD_NNNN_aciklama.sql` olarak eklenir ve `migrations`
 * tablosuna yazilir.  DOCS.md 8.6
 */

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;

final class Migrator
{
    public function __construct(private Database $db)
    {
    }

    /** Klasordeki tum goc dosyalari, ad sirasina gore. */
    public function available(): array
    {
        $files = glob(ARC_ROOT . '/db/migrations/*.sql') ?: [];
        $names = array_map('basename', $files);
        sort($names, SORT_STRING);
        return $names;
    }

    /** Uygulanmis goc dosyalari. */
    public function applied(): array
    {
        if (!$this->db->tableExists('migrations')) {
            return [];
        }
        return $this->db->column('SELECT filename FROM migrations ORDER BY filename');
    }

    /** Henuz uygulanmamis dosyalar. */
    public function pending(): array
    {
        return array_values(array_diff($this->available(), $this->applied()));
    }

    /** Tek bir goc dosyasini uygular ve kaydeder. */
    public function apply(string $filename): void
    {
        $filename = basename($filename);
        $path     = ARC_ROOT . '/db/migrations/' . $filename;

        if (!is_file($path)) {
            throw new RuntimeException('Göç dosyası bulunamadı: ' . $filename);
        }

        $sql = (string) file_get_contents($path);
        $this->runSqlScript($sql);

        $this->db->insert('migrations', ['filename' => $filename]);
    }

    /** Bekleyen tum gocleri uygular ve uygulanan dosya adlarini dondurur. */
    public function migrate(): array
    {
        $done = [];
        foreach ($this->pending() as $file) {
            $this->apply($file);
            $done[] = $file;
        }
        return $done;
    }

    /**
     * Cok ifadeli SQL betigini calistirir.
     *
     * Betikler yalnizca depodan gelir, kullanicidan degil; yine de yorumlar
     * temizlenir ve ifadeler tek tek calistirilir.
     */
    public function runSqlScript(string $sql): int
    {
        $count = 0;
        foreach (self::splitStatements($sql) as $statement) {
            $this->db->pdo()->exec($statement);
            $count++;
        }
        return $count;
    }

    /**
     * Betigi ifadelere ayirir.
     *
     * Dize icindeki noktali virgul ayirici sayilmaz.
     *
     * @return string[]
     */
    public static function splitStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $inString   = false;
        $quote      = '';
        $length     = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $current .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $sql[++$i];
                    continue;
                }
                if ($char === $quote) {
                    $inString = false;
                }
                continue;
            }

            // Satir yorumu
            if (($char === '-' && ($sql[$i + 1] ?? '') === '-') || $char === '#') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                $current .= "\n";
                continue;
            }

            // Blok yorumu
            if ($char === '/' && ($sql[$i + 1] ?? '') === '*') {
                $end = strpos($sql, '*/', $i + 2);
                $i   = $end === false ? $length : $end + 1;
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $inString = true;
                $quote    = $char;
                $current .= $char;
                continue;
            }

            if ($char === ';') {
                $trimmed = trim($current);
                if ($trimmed !== '') {
                    $statements[] = $trimmed;
                }
                $current = '';
                continue;
            }

            $current .= $char;
        }

        $trimmed = trim($current);
        if ($trimmed !== '') {
            $statements[] = $trimmed;
        }

        return $statements;
    }
}
