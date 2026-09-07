<?php
/**
 * Bekleyen goc dosyalarini uygular.
 *
 * `db/migrations/*.sql` dosyalari ad sirasina gore calistirilir ve
 * `migrations` tablosuna yazilir. Uygulanmis dosya tekrar calistirilmaz.
 * DOCS.md 8.6
 *
 * Kullanim: php tools/migrate.php [--dry]
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Bu araç yalnızca komut satırından çalışır.\n");
}

define('ARC_ROOT', dirname(__DIR__));
require ARC_ROOT . '/app/autoload.php';

use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Migrator;

Config::load();
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));

$dry = in_array('--dry', $argv, true);

try {
    $db = Database::instance();
    $db->connect();
} catch (Throwable $e) {
    fwrite(STDERR, 'Veritabanına bağlanılamadı: ' . $e->getMessage() . "\n");
    exit(1);
}

$migrator = new Migrator($db);
$pending  = $migrator->pending();

if (!$pending) {
    echo "Bekleyen göç yok.\n";
    exit(0);
}

echo count($pending) . " bekleyen göç bulundu:\n";
foreach ($pending as $file) {
    echo '  - ' . $file . "\n";
}

if ($dry) {
    echo "Kuru çalışma; hiçbir şey uygulanmadı.\n";
    exit(0);
}

foreach ($pending as $file) {
    try {
        $migrator->apply($file);
        echo "Uygulandı: {$file}\n";
    } catch (Throwable $e) {
        fwrite(STDERR, "Başarısız: {$file}\n  " . $e->getMessage() . "\n");
        exit(1);
    }
}

echo "Tüm göçler uygulandı.\n";
exit(0);
