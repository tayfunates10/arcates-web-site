<?php
/**
 * Eski ziyaret kayitlarini gunluk tabloya toplar.
 *
 * `visits` tablosu hizla buyur. Gunluk cron 90 gunden eski kayitlari
 * `visits_daily`'ye toplar ve siler.  DOCS.md 8.4
 *
 * cPanel gorevi:
 *   30 3 * * * php /home/kullanici/arcates-web-site/tools/rollup_visits.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Bu arac yalnizca komut satirindan calisir.\n");
}

define('ARC_ROOT', dirname(__DIR__));
require ARC_ROOT . '/app/autoload.php';

use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Logger;
use Arcates\Core\Visits;

Config::load();
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));

try {
    Database::instance()->connect();
} catch (Throwable $e) {
    fwrite(STDERR, 'Veritabanina baglanilamadi: ' . $e->getMessage() . "\n");
    exit(1);
}

$days = (int) Config::get('privacy.visit_retention_days', 90);

try {
    $result = Visits::rollup($days);

    Logger::info('Ziyaret kayitlari toplandi', $result);
    echo "Toplanan satir : {$result['rolled']}\n";
    echo "Silinen kayit  : {$result['deleted']}\n";
} catch (Throwable $e) {
    Logger::error('Toplama basarisiz: ' . $e->getMessage());
    fwrite(STDERR, 'Toplama basarisiz: ' . $e->getMessage() . "\n");
    exit(1);
}

exit(0);
