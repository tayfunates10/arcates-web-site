<?php
/**
 * Veritabani yedegi alma ve geri yukleme.
 *
 * Harici arac gerektirmez; sema ve veriler PDO ile okunup gzip'lenmis SQL
 * dosyasina yazilir. Son 10 yedek tutulur.  DOCS.md 9.11, 13 — test F-19
 */

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;
use Throwable;

final class Backup
{
    /** Tutulacak yedek sayisi. DOCS.md 9.11 */
    public const KEEP = 10;

    public static function directory(): string
    {
        $dir = ARC_ROOT . '/storage/backups';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    /**
     * Yedek alir ve dosya adini dondurur.
     *
     * @throws RuntimeException Yazilamazsa.
     */
    public static function create(): string
    {
        $db  = Database::instance();
        $pdo = $db->pdo();

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
        if (!$tables) {
            throw new RuntimeException('Yedeklenecek tablo bulunamadi.');
        }

        $filename = 'arcates-' . date('Y-m-d-His') . '.sql.gz';
        $path     = self::directory() . '/' . $filename;

        $handle = @gzopen($path, 'wb9');
        if ($handle === false) {
            throw new RuntimeException('Yedek dosyasi acilamadi: ' . $path);
        }

        $write = static function (string $text) use ($handle): void {
            gzwrite($handle, $text);
        };

        $write("-- Arcates Web Site yedegi\n");
        $write('-- Tarih: ' . date('c') . "\n");
        $write("SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\n\n");

        foreach ($tables as $table) {
            $safe = Database::identifier((string) $table);

            $create = $pdo->query('SHOW CREATE TABLE `' . $safe . '`')->fetch(\PDO::FETCH_NUM);
            $write('DROP TABLE IF EXISTS `' . $safe . "`;\n");
            $write(($create[1] ?? '') . ";\n\n");

            $statement = $pdo->query('SELECT * FROM `' . $safe . '`');
            $rows      = 0;

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $columns = [];
                $values  = [];

                foreach ($row as $column => $value) {
                    $columns[] = '`' . Database::identifier((string) $column) . '`';
                    if ($value === null) {
                        $values[] = 'NULL';
                    } elseif (is_int($value) || is_float($value)) {
                        $values[] = (string) $value;
                    } else {
                        $values[] = $pdo->quote((string) $value);
                    }
                }

                $write('INSERT INTO `' . $safe . '` (' . implode(', ', $columns) . ') VALUES ('
                    . implode(', ', $values) . ");\n");
                $rows++;
            }

            if ($rows > 0) {
                $write("\n");
            }
        }

        $write("SET FOREIGN_KEY_CHECKS = 1;\n");
        gzclose($handle);
        @chmod($path, 0640);

        self::prune();

        return $filename;
    }

    /** Yedek listesi, yeniden eskiye. */
    public static function listing(): array
    {
        $files = glob(self::directory() . '/*.sql.gz') ?: [];

        $out = [];
        foreach ($files as $file) {
            $out[] = [
                'filename' => basename($file),
                'size'     => (int) filesize($file),
                'created'  => date('Y-m-d H:i:s', (int) filemtime($file)),
                'time'     => (int) filemtime($file),
            ];
        }

        usort($out, static fn (array $a, array $b): int => $b['time'] <=> $a['time']);

        return $out;
    }

    /** En eski yedekleri siler; son KEEP tanesi kalir. */
    public static function prune(): int
    {
        $files   = self::listing();
        $deleted = 0;

        foreach (array_slice($files, self::KEEP) as $file) {
            if (@unlink(self::directory() . '/' . $file['filename'])) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /** Yedek dosyasinin tam yolu; klasor disina cikilamaz. */
    public static function path(string $filename): ?string
    {
        $filename = basename($filename);
        if (preg_match('/^arcates-\d{4}-\d{2}-\d{2}-\d{6}\.sql\.gz$/', $filename) !== 1) {
            return null;
        }

        $path = self::directory() . '/' . $filename;
        return is_file($path) ? $path : null;
    }

    /**
     * Yedegi geri yukler.  DOCS.md 9.11, test F-19
     *
     * @return int Calistirilan ifade sayisi.
     */
    public static function restore(string $filename): int
    {
        $path = self::path($filename);
        if ($path === null) {
            throw new RuntimeException('Yedek dosyasi bulunamadi.');
        }

        $sql = @gzfile($path);
        if ($sql === false) {
            throw new RuntimeException('Yedek dosyasi okunamadi.');
        }

        $migrator = new Migrator(Database::instance());

        try {
            return $migrator->runSqlScript(implode('', $sql));
        } catch (Throwable $e) {
            Logger::exception($e);
            throw new RuntimeException('Geri yukleme basarisiz: ' . $e->getMessage(), 0, $e);
        }
    }

    public static function delete(string $filename): bool
    {
        $path = self::path($filename);
        return $path !== null && @unlink($path);
    }
}
