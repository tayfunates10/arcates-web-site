<?php
/**
 * Yayin oncesi teslim listesi denetimi.
 *
 * DOCS.md 17 — "Yayin oncesi teslim listesi" maddelerinin makine tarafindan
 * denetlenebilir olanlarini kontrol eder. Insan gozuyle bakilmasi gerekenler
 * (form testi, Search Console gonderimi, Rich Results denetimi) listede
 * "elle" olarak isaretlenir.
 *
 * Kullanim: php tools/preflight.php
 * Cikis kodu: engelleyici sorun varsa 1, yoksa 0.
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
use Arcates\Core\Seo;
use Arcates\Core\Settings;
use Arcates\Controllers\Front\RobotsController;
use Arcates\Controllers\Front\SitemapController;

Config::load();
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));

$rows     = [];
$blocking = 0;

/**
 * @param string $level 'engel' | 'uyari' | 'elle'
 */
function check(string $label, ?bool $ok, string $detail = '', string $level = 'engel'): void
{
    global $rows, $blocking;

    $rows[] = ['label' => $label, 'ok' => $ok, 'detail' => $detail, 'level' => $level];

    if ($ok === false && $level === 'engel') {
        $blocking++;
    }
}

// --- Yapilandirma -----------------------------------------------------------

check('config/config.php mevcut', Config::exists());
check('display_errors kapali (app.debug)', !(bool) Config::get('app.debug', false),
    'app.debug = ' . (Config::get('app.debug') ? 'true' : 'false'));
check('base_url https ile basliyor', str_starts_with((string) Config::get('app.base_url', ''), 'https://'),
    (string) Config::get('app.base_url', ''));
check('Oturum cerezi secure', (bool) Config::get('security.cookie_secure', true));

// --- Kurulum ve dosya erisimi -----------------------------------------------

check('/install erisilemez (installed.lock)', is_file(ARC_ROOT . '/storage/installed.lock'));
check('config klasoru web kokunun disinda', !is_dir(ARC_ROOT . '/public/config'));
check('.git klasoru web kokunun disinda', !is_dir(ARC_ROOT . '/public/.git'));

$uploads = (string) @file_get_contents(ARC_ROOT . '/public/uploads/.htaccess');
check('uploads klasorunde PHP kapali', str_contains($uploads, 'php_flag engine off'));

$htaccess = (string) @file_get_contents(ARC_ROOT . '/public/.htaccess');
check('SSL ve https yonlendirmesi', str_contains($htaccess, 'RewriteCond %{HTTPS} off'));

$activeRules = 0;
foreach (explode("\n", $htaccess) as $line) {
    $line = trim($line);
    if ($line !== '' && $line[0] !== '#' && str_contains($line, '[R=301,L]')) {
        $activeRules++;
    }
}
check('www tercihi tek yonde sabit', $activeRules === 2, $activeRules . ' etkin 301 kurali');

// --- Yazilabilirlik ---------------------------------------------------------

foreach (['storage', 'storage/logs', 'storage/backups', 'public/uploads'] as $dir) {
    check('Yazilabilir: ' . $dir, is_writable(ARC_ROOT . '/' . $dir));
}

// --- Veritabani ve icerik ---------------------------------------------------

$db = Database::instance();

if (!$db->canConnect()) {
    check('Veritabani baglantisi', false, 'baglanilamadi');
} else {
    check('Veritabani baglantisi', true);

    // Guclu sifre kontrolu yapilamaz (karma saklanir); en az bir admin olmali.
    $admins = (int) $db->value('SELECT COUNT(*) FROM users WHERE role = :r AND status = 1', [':r' => 'admin']);
    check('En az bir etkin yonetici', $admins > 0, $admins . ' yonetici');
    check('Panel sifresi guclu', null, 'elle dogrulanir', 'elle');

    // Favicon ve OG gorseli
    check('Favicon mevcut', is_file(ARC_ROOT . '/public/assets/img/favicon.svg'));
    check('Varsayilan OG gorseli mevcut', is_file(ARC_ROOT . '/public/assets/img/og-default.png'));

    // NAP bilgileri
    $nap = ['nap_name', 'nap_phone', 'nap_district', 'nap_city'];
    $eksikNap = [];
    foreach ($nap as $key) {
        if (trim((string) Settings::get($key, '')) === '') {
            $eksikNap[] = $key;
        }
    }
    check('NAP bilgileri dolu', $eksikNap === [], $eksikNap ? 'eksik: ' . implode(', ', $eksikNap) : '');
    check('NAP Google Isletme Profili ile ayni', null, 'elle dogrulanir', 'elle');

    // Sitemap ve robots
    $entries = (new SitemapController())->entries();
    check('sitemap.xml icerik uretiyor', count($entries) > 0, count($entries) . ' adres');
    check('sitemap.xml Search Console\'a gonderildi', null, 'elle dogrulanir', 'elle');

    $robots = (new RobotsController())->body();
    check('robots.txt sitemap satiri iceriyor', str_contains($robots, 'Sitemap:'));
    check('robots.txt panel yolunu engelliyor', str_contains($robots, 'Disallow: /' . trim((string) Config::get('app.admin_path', 'panel'), '/')));

    // 404 sayfasi
    check('404 sayfasi mevcut', is_file(ARC_ROOT . '/views/errors/404.php'));

    // Icerik skoru: guclu uyari tasiyan sayfa olmamali
    $strong = [];
    $pages = $db->all(
        'SELECT p.id AS page_id, p.type, p.district, t.*
           FROM pages p JOIN page_translations t ON t.page_id = p.id
          WHERE p.status = :s AND t.lang = :l',
        [':s' => 'published', ':l' => (string) Config::get('lang.default', 'tr')]
    );

    foreach ($pages as $page) {
        $score = Seo::score(
            ['id' => (int) $page['page_id'], 'type' => $page['type'], 'district' => $page['district']],
            $page
        );
        foreach ($score['issues'] as $issue) {
            if ($issue['level'] === 'strong') {
                $strong[] = $page['slug'] . ': ' . $issue['check'];
            }
        }
    }
    check('Guclu icerik uyarisi yok', $strong === [], $strong ? implode('; ', array_slice($strong, 0, 3)) : count($pages) . ' sayfa denetlendi');

    // Ilce sayfalari — bolum 4.7
    $locations = $db->all(
        'SELECT p.id, p.district, t.slug, t.word_count
           FROM pages p JOIN page_translations t ON t.page_id = p.id
          WHERE p.type = :t AND p.status = :s AND t.lang = :l',
        [':t' => 'location', ':s' => 'published', ':l' => (string) Config::get('lang.default', 'tr')]
    );

    $thin = $noRef = $noFaq = [];
    foreach ($locations as $loc) {
        if ((int) $loc['word_count'] < Seo::WORDS_MIN_LOCATION) {
            $thin[] = $loc['slug'];
        }
        if ((int) $db->value('SELECT COUNT(*) FROM projects WHERE district = :d AND status = :s', [':d' => $loc['district'], ':s' => 'published']) === 0) {
            $noRef[] = $loc['slug'];
        }
        if ((int) $db->value('SELECT COUNT(*) FROM faq_page WHERE page_id = :id', [':id' => (int) $loc['id']]) === 0) {
            $noFaq[] = $loc['slug'];
        }
    }

    check('Ilce sayfalari 500+ kelime', $thin === [], $thin ? implode(', ', $thin) : count($locations) . ' ilce');
    check('Her ilcenin referansi var', $noRef === [], $noRef ? implode(', ', $noRef) : '');
    check('Her ilcenin SSS kaydi var', $noFaq === [], $noFaq ? implode(', ', $noFaq) : '');

    // Demo icerik
    $demo = (int) $db->value('SELECT COUNT(*) FROM projects WHERE client_name LIKE :q', [':q' => 'Ornek %']);
    check('Demo icerik temizlendi', $demo === 0, $demo > 0 ? $demo . ' ornek referans kayitli' : '', 'uyari');

    // Form
    check('Form test edildi, e-posta ulasiyor', null, 'elle dogrulanir', 'elle');

    // Yedek
    $backups = Backup::listing();
    check('En az bir yedek mevcut', $backups !== [], count($backups) . ' yedek');
    check('Otomatik yedek cron\'u kuruldu', null, 'elle dogrulanir', 'elle');
    check('Yedekten bir kez geri yuklendi', null, 'elle dogrulanir', 'elle');

    // Yapisal veri
    check('Yapisal veri Rich Results testinden gecti', null, 'elle dogrulanir', 'elle');
    check('Analytics baglandi', trim((string) Settings::get('analytics_code', '')) !== '', '', 'uyari');
}

// --- Cikti ------------------------------------------------------------------

echo "\nArcates — yayin oncesi teslim listesi (DOCS.md 17)\n";
echo str_repeat('=', 70), "\n";

foreach ($rows as $row) {
    $mark = match (true) {
        $row['ok'] === true  => '  [ok]  ',
        $row['ok'] === false => $row['level'] === 'engel' ? '  [!!]  ' : '  [uy]  ',
        default              => '  [el]  ',
    };
    printf("%s%-44s %s\n", $mark, $row['label'], $row['detail']);
}

echo str_repeat('=', 70), "\n";
echo "[ok] gecti   [!!] engelleyici   [uy] uyari   [el] elle dogrulanir\n";

if ($blocking > 0) {
    printf("\n%d engelleyici madde var; yayina cikmadan once giderin.\n\n", $blocking);
    exit(1);
}

echo "\nEngelleyici madde yok. Elle dogrulanacak maddeleri tamamlayin.\n\n";
exit(0);
