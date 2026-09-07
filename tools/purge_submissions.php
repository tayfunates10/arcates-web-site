<?php
/**
 * Saklama suresi dolan form kayitlarini siler.
 *
 * KVKK geregi kayitlar sinirsiz saklanmaz. Sure panelden ayarlanir,
 * bu arac gunluk gorevle calisir.  DOCS.md 9.9, 10.9
 *
 * Kullanim: php tools/purge_submissions.php [--dry]
 *
 * cPanel gorev ornegi:
 *   15 3 * * * php /home/kullanici/arcates-web-site/tools/purge_submissions.php
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
use Arcates\Core\Settings;
use Arcates\Models\Submission;

Config::load();
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));

try {
    Database::instance()->connect();
} catch (Throwable $e) {
    fwrite(STDERR, 'Veritabanina baglanilamadi: ' . $e->getMessage() . "\n");
    exit(1);
}

$days = Settings::getInt('submission_days', (int) Config::get('privacy.submission_retention_days', 730));
$days = max(30, $days);

$cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

$expired = (int) Database::instance()->value(
    'SELECT COUNT(*) FROM submissions WHERE created_at < :cutoff',
    [':cutoff' => $cutoff]
);

echo "Saklama suresi: {$days} gun (kesim: {$cutoff})\n";
echo "Suresi dolan kayit: {$expired}\n";

if (in_array('--dry', $argv, true)) {
    echo "Kuru calisma; hicbir kayit silinmedi.\n";
    exit(0);
}

if ($expired === 0) {
    exit(0);
}

$deleted = Submission::purgeExpired($days);
Logger::info('Form kayitlari temizlendi', ['silinen' => $deleted, 'gun' => $days]);

echo "{$deleted} kayit silindi.\n";
exit(0);
