<?php
/**
 * Gunluk veritabani yedegi.
 *
 * Son 10 yedek tutulur, eskiler silinir.  DOCS.md 9.11, 13
 *
 * cPanel gorevi:
 *   0 3 * * * php /home/kullanici/arcates-web-site/tools/backup.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Bu arac yalnizca komut satirindan calisir.\n");
}

define('ARC_ROOT', dirname(__DIR__));
require ARC_ROOT . '/app/autoload.php';

use Arcates\Core\Backup;
use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Logger;

Config::load();
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));

try {
    Database::instance()->connect();
} catch (Throwable $e) {
    fwrite(STDERR, 'Veritabanina baglanilamadi: ' . $e->getMessage() . "\n");
    exit(1);
}

try {
    $filename = Backup::create();
    $path     = Backup::path($filename);
    $size     = $path !== null ? filesize($path) : 0;

    Logger::info('Yedek alindi', ['dosya' => $filename, 'boyut' => $size]);
    echo "Yedek alindi: {$filename} (" . number_format((float) $size / 1024, 1) . " KB)\n";
    echo 'Saklanan yedek: ' . count(Backup::listing()) . "\n";
} catch (Throwable $e) {
    Logger::error('Yedek alinamadi: ' . $e->getMessage());
    fwrite(STDERR, 'Yedek alinamadi: ' . $e->getMessage() . "\n");
    exit(1);
}

exit(0);
