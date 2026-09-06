<?php
/**
 * Test yardimcilari.
 *
 * Veritabani gerektiren testler icin gecici bir MySQL semasi hazirlar.
 * Baglanti bilgisi verilmemisse ilgili testler `skip()` ile atlanir; boylece
 * CI veritabanisiz da yesil kalir.  DOCS.md 14.1
 *
 * Ortam degiskenleri:
 *   ARC_TEST_DB_HOST, ARC_TEST_DB_PORT, ARC_TEST_DB_NAME,
 *   ARC_TEST_DB_USER, ARC_TEST_DB_PASS
 */

declare(strict_types=1);

use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Migrator;
use Arcates\Core\Seeder;
use Arcates\Core\Settings;

/** Testlerde kullanilan temel yapilandirma. */
function arc_test_config(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    Config::load([
        'app' => [
            'base_url'   => 'https://ornek.test',
            'env'        => 'local',
            'debug'      => true,
            'timezone'   => 'Europe/Istanbul',
            'name'       => 'Arcates Test',
            'admin_path' => 'panel',
        ],
        'db' => [
            'host'    => getenv('ARC_TEST_DB_HOST') ?: 'localhost',
            'port'    => (int) (getenv('ARC_TEST_DB_PORT') ?: 3306),
            'name'    => getenv('ARC_TEST_DB_NAME') ?: '',
            'user'    => getenv('ARC_TEST_DB_USER') ?: '',
            'pass'    => getenv('ARC_TEST_DB_PASS') ?: '',
            'charset' => 'utf8mb4',
        ],
        'lang'     => ['default' => 'tr', 'available' => ['tr', 'en', 'de', 'ar']],
        'mail'     => ['method' => 'log', 'from' => 'test@ornek.test', 'from_name' => 'Test', 'to' => 'kayit@ornek.test'],
        'security' => [
            'session_name'     => 'arcsid',
            'session_idle'     => 7200,
            'login_max_tries'  => 5,
            'login_lock'       => 900,
            'password_min'     => 10,
            'form_max_hourly'  => 5,
            'form_min_seconds' => 3,
            'cookie_secure'    => false,
        ],
        'upload' => [
            'max_size'    => 5 * 1024 * 1024,
            'allowed_ext' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'],
            'variants'    => ['thumb' => 320, 'medium' => 768, 'large' => 1600],
        ],
        'privacy' => ['submission_retention_days' => 730, 'visit_retention_days' => 90],
    ]);

    if (!isset($_SESSION)) {
        $_SESSION = [];
    }
    $_SERVER['REMOTE_ADDR']     = $_SERVER['REMOTE_ADDR'] ?? '203.0.113.10';
    $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? 'ArcatesTest/1.0';
}

/**
 * Test veritabani. Yapilandirilmamissa null doner.
 *
 * Ilk cagrida sema uygulanir ve tohum verisi yazilir.
 */
function arc_test_db(): ?Database
{
    static $db = null;
    static $tried = false;

    arc_test_config();

    if ($tried) {
        return $db;
    }
    $tried = true;

    if ((string) Config::get('db.name', '') === '') {
        return null;
    }

    $candidate = Database::instance();
    if (!$candidate->canConnect()) {
        return null;
    }

    // Onceki kalintilari temizle, semayi yeniden kur.
    $pdo = $candidate->pdo();
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
        $pdo->exec('DROP TABLE IF EXISTS `' . $table . '`');
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    $migrator = new Migrator($candidate);
    $migrator->runSqlScript((string) file_get_contents(ARC_ROOT . '/db/schema.sql'));
    foreach ($migrator->pending() as $file) {
        $candidate->insert('migrations', ['filename' => $file]);
    }

    (new Seeder($candidate))->run();
    Settings::flush();
    Lang::reset();

    return $db = $candidate;
}

/** Veritabani yoksa testi atlar. */
function arc_need_db(): Database
{
    $db = arc_test_db();
    if ($db === null) {
        skip('Test veritabani yapilandirilmadi (ARC_TEST_DB_NAME).');
    }
    return $db;
}

/** Test icin oturum durumunu sifirlar. */
function arc_reset_session(): void
{
    $_SESSION = [];
}
