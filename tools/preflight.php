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
    exit("Bu araç yalnızca komut satırından çalışır.\n");
}

define('ARC_ROOT', dirname(__DIR__));
require ARC_ROOT . '/app/autoload.php';

use Arcates\Core\Backup;
use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Migrator;
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
check('display_errors kapalı (app.debug)', !(bool) Config::get('app.debug', false),
    'app.debug = ' . (Config::get('app.debug') ? 'true' : 'false'));
check('base_url https ile başlıyor', str_starts_with((string) Config::get('app.base_url', ''), 'https://'),
    (string) Config::get('app.base_url', ''));
check('Oturum çerezi secure', (bool) Config::get('security.cookie_secure', true));

// --- Kurulum ve dosya erisimi -----------------------------------------------

check('/install erişilemez (installed.lock)', is_file(ARC_ROOT . '/storage/installed.lock'));
check('config klasörü web kökünün dışında', !is_dir(ARC_ROOT . '/public/config'));
check('.git klasörü web kökünün dışında', !is_dir(ARC_ROOT . '/public/.git'));

$uploads = (string) @file_get_contents(ARC_ROOT . '/public/uploads/.htaccess');
check('uploads klasöründe PHP kapalı', str_contains($uploads, 'php_flag engine off'));

$htaccess = (string) @file_get_contents(ARC_ROOT . '/public/.htaccess');
check('SSL ve https yönlendirmesi', str_contains($htaccess, 'RewriteCond %{HTTPS} off'));

$activeRules = 0;
foreach (explode("\n", $htaccess) as $line) {
    $line = trim($line);
    if ($line !== '' && $line[0] !== '#' && str_contains($line, '[R=301,L]')) {
        $activeRules++;
    }
}
check('www tercihi tek yönde sabit', $activeRules === 2, $activeRules . ' etkin 301 kuralı');

// --- Yazilabilirlik ---------------------------------------------------------

foreach (['storage', 'storage/logs', 'storage/backups', 'public/uploads'] as $dir) {
    check('Yazılabilir: ' . $dir, is_writable(ARC_ROOT . '/' . $dir));
}

// --- Veritabani ve icerik ---------------------------------------------------

$db = Database::instance();

if (!$db->canConnect()) {
    check('Veritabanı bağlantısı', false, 'baglanilamadi');
} else {
    check('Veritabanı bağlantısı', true);

    // Bekleyen goc, kurulmus sitede yeni surumun eksik calismasi demektir.
    // Sema disi degisiklikler de gocle tasiniyor: DOCS.md 8.6.
    $pending = (new Migrator($db))->pending();
    check(
        'Bekleyen göç yok',
        $pending === [],
        $pending ? 'php tools/migrate.php — ' . implode(', ', $pending) : ''
    );

    // Guclu sifre kontrolu yapilamaz (karma saklanir); en az bir admin olmali.
    $admins = (int) $db->value('SELECT COUNT(*) FROM users WHERE role = :r AND status = 1', [':r' => 'admin']);
    check('En az bir etkin yönetici', $admins > 0, $admins . ' yönetici');
    check('Panel şifresi güçlü', null, 'elle doğrulanır', 'elle');

    // Favicon ve OG gorseli
    check('Favicon mevcut', is_file(ARC_ROOT . '/public/assets/img/favicon-32.png'));
    check('Varsayılan OG görseli mevcut', is_file(ARC_ROOT . '/public/assets/img/og-default.png'));

    // NAP bilgileri
    $nap = ['nap_name', 'nap_phone', 'nap_district', 'nap_city'];
    $eksikNap = [];
    foreach ($nap as $key) {
        if (trim((string) Settings::get($key, '')) === '') {
            $eksikNap[] = $key;
        }
    }
    check('NAP bilgileri dolu', $eksikNap === [], $eksikNap ? 'eksik: ' . implode(', ', $eksikNap) : '');
    check('NAP Google İşletme Profili ile aynı', null, 'elle doğrulanır', 'elle');

    // Sitemap ve robots
    $entries = (new SitemapController())->entries();
    check('sitemap.xml içerik üretiyor', count($entries) > 0, count($entries) . ' adres');
    check('sitemap.xml Search Console\'a gönderildi', null, 'elle doğrulanır', 'elle');

    $robots = (new RobotsController())->body();
    check('robots.txt sitemap satırı içeriyor', str_contains($robots, 'Sitemap:'));
    check('robots.txt panel yolunu engelliyor', str_contains($robots, 'Disallow: /' . trim((string) Config::get('app.admin_path', 'panel'), '/')));

    // 404 sayfasi
    check('404 sayfası mevcut', is_file(ARC_ROOT . '/views/errors/404.php'));

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
    check('Güçlü içerik uyarısı yok', $strong === [], $strong ? implode('; ', array_slice($strong, 0, 3)) : count($pages) . ' sayfa denetlendi');

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

    check('İlçe sayfaları 500+ kelime', $thin === [], $thin ? implode(', ', $thin) : count($locations) . ' ilçe');
    check('Her ilçenin örnek sitesi var', $noRef === [], $noRef ? implode(', ', $noRef) : '');
    check('Her ilçenin SSS kaydı var', $noFaq === [], $noFaq ? implode(', ', $noFaq) : '');

    // Ornek site kayitlari. Bunlar teslim edilmis is degildir; ziyaretciye
    // `projects_notice` notuyla acikca soylenmeleri sarttir. DOCS.md 9.4
    $demo   = (int) $db->value('SELECT COUNT(*) FROM projects WHERE client_name LIKE :q', [':q' => 'Örnek %']);
    $notice = trim((string) Settings::get('projects_notice', ''));

    if ($demo === 0) {
        check('Örnek kayıtlar gerçek işlerle değiştirildi', true, '');
    } else {
        check(
            'Örnek kayıtlar not ile işaretli',
            $notice !== '',
            $notice !== ''
                ? $demo . ' örnek site, not görünüyor'
                : $demo . ' örnek site var ama "Örnek site notu" boş',
            'engel'
        );
    }

    // Form
    check('Form test edildi, e-posta ulaşıyor', null, 'elle doğrulanır', 'elle');

    // Yedek
    $backups = Backup::listing();
    check('En az bir yedek mevcut', $backups !== [], count($backups) . ' yedek');
    check('Otomatik yedek cron\'u kuruldu', null, 'elle doğrulanır', 'elle');
    check('Yedekten bir kez geri yüklendi', null, 'elle doğrulanır', 'elle');

    // Yapisal veri
    check('Yapısal veri Rich Results testinden geçti', null, 'elle doğrulanır', 'elle');
    check('Analytics bağlandı', trim((string) Settings::get('analytics_code', '')) !== '', '', 'uyari');
}

// --- Cikti ------------------------------------------------------------------

echo "\nArcates — yayın öncesi teslim listesi (DOCS.md 17)\n";
echo str_repeat('=', 70), "\n";

foreach ($rows as $row) {
    $mark = match (true) {
        $row['ok'] === true  => '  [ok]  ',
        $row['ok'] === false => $row['level'] === 'engel' ? '  [!!]  ' : '  [uy]  ',
        default              => '  [el]  ',
    };
    // Turkce harfler cok baytlidir; hizalama karakter sayisina gore yapilir.
    $pad = max(0, 44 - mb_strlen((string) $row['label'], 'UTF-8'));
    echo $mark . $row['label'] . str_repeat(' ', $pad) . ' ' . $row['detail'] . "\n";
}

echo str_repeat('=', 70), "\n";
echo "[ok] geçti   [!!] engelleyici   [uy] uyarı   [el] elle doğrulanır\n";

if ($blocking > 0) {
    printf("\n%d engelleyici madde var; yayına çıkmadan önce giderin.\n\n", $blocking);
    exit(1);
}

echo "\nEngelleyici madde yok. Elle doğrulanacak maddeleri tamamlayın.\n\n";
exit(0);
