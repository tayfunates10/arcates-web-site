<?php
/**
 * Baslangic icerigini yazar.
 *
 * DOCS.md 4 (sayfa envanteri) ve 17 (faz 12) — kurulum sonrasi calistirilir.
 * Var olan kayitlarin uzerine yazmaz; yalnizca eksik olanlari ekler.
 *
 * Kullanim:
 *   php tools/seed_content.php          eksikleri ekler
 *   php tools/seed_content.php --dry    ne yapilacagini yazar, degistirmez
 *   php tools/seed_content.php --force  var olanlarin da icerigini tazeler
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
use Arcates\Core\Logger;
use Arcates\Core\Security;
use Arcates\Core\Seeder;
use Arcates\Models\Faq;
use Arcates\Models\Page;
use Arcates\Models\Post;
use Arcates\Models\Project;

Config::load();
date_default_timezone_set((string) Config::get('app.timezone', 'Europe/Istanbul'));

$dry   = in_array('--dry', $argv, true);
$force = in_array('--force', $argv, true);

try {
    $db = Database::instance();
    $db->connect();
} catch (Throwable $e) {
    fwrite(STDERR, 'Veritabanına bağlanılamadı: ' . $e->getMessage() . "\n");
    exit(1);
}

if (!$db->tableExists('pages')) {
    fwrite(STDERR, "Şema uygulanmamış. Önce /install adımlarını tamamlayın.\n");
    exit(1);
}

// Diller, ayarlar, anasayfa bolumleri ve ilce noktalari.
if (!$dry) {
    (new Seeder($db))->run();
}
echo "Temel tohum verisi hazır.\n";

$added   = 0;
$skipped = 0;
$slugToPage = [];

/** Sayfa yazar; var olan kayda dokunmaz (--force disinda). */
$writePage = function (array $item, string $type) use ($db, $dry, $force, &$added, &$skipped, &$slugToPage): void {
    $existing = $db->first(
        'SELECT page_id FROM page_translations WHERE lang = :lang AND slug = :slug',
        [':lang' => 'tr', ':slug' => $item['slug']]
    );

    if ($existing !== null && !$force) {
        $slugToPage[$item['slug']] = (int) $existing['page_id'];
        $skipped++;
        return;
    }

    if ($dry) {
        echo '  + ' . $type . ' /' . $item['slug'] . "\n";
        $added++;
        return;
    }

    $pageId = Page::save(
        [
            'type'     => $type,
            'template' => Page::TEMPLATES[$type] ?? 'page',
            'status'   => 'published',
            'district' => $item['district'] ?? null,
            'sort'     => (int) ($item['sort'] ?? 0),
        ],
        ['tr' => [
            'title'            => $item['title'],
            'slug'             => $item['slug'],
            'excerpt'          => $item['excerpt'] ?? null,
            'content'          => $item['content'],
            'meta_title'       => $item['meta_title'] ?? null,
            'meta_description' => $item['meta_description'] ?? null,
            // KVKK ve gizlilik sayfalari da indekslenir. DOCS.md 10.9
            'robots'           => 'index,follow',
            'schema_type'      => $type === 'page' ? null : 'Service',
        ]],
        $existing !== null ? (int) $existing['page_id'] : null
    );

    $slugToPage[$item['slug']] = $pageId;
    $added++;
    echo '  + ' . $type . ' /' . $item['slug'] . "\n";
};

// --- Sayfalar ---------------------------------------------------------------

echo "\nAna sayfalar ve hizmetler:\n";
foreach (require ARC_ROOT . '/db/seed/pages.php' as $item) {
    $writePage($item, $item['type']);
}

echo "\nİlçe sayfaları (DOCS.md 4.3, 4.7):\n";
foreach (require ARC_ROOT . '/db/seed/locations.php' as $item) {
    $words = Security::wordCount($item['content']);
    if ($words < 500) {
        fwrite(STDERR, '  ! ' . $item['slug'] . " yalnızca {$words} kelime; bölüm 4.7 en az 500 kelime istiyor.\n");
    }
    $writePage($item + ['sort' => 30], 'location');
}

echo "\nSektör sayfaları:\n";
foreach (require ARC_ROOT . '/db/seed/sectors.php' as $item) {
    $writePage($item, 'sector');
}

// --- Referanslar ------------------------------------------------------------

echo "\nReferanslar:\n";
$projectsAdded = 0;
foreach (require ARC_ROOT . '/db/seed/projects.php' as $item) {
    $exists = $db->first(
        'SELECT project_id FROM project_translations WHERE lang = :lang AND slug = :slug',
        [':lang' => 'tr', ':slug' => $item['slug']]
    );

    if ($exists !== null && !$force) {
        continue;
    }

    if (!$dry) {
        Project::save(
            [
                'client_name' => $item['client'],
                'sector'      => $item['sector'],
                'district'    => $item['district'],
                'status'      => 'published',
                'sort'        => 0,
            ],
            ['tr' => [
                'title'   => $item['title'],
                'slug'    => $item['slug'],
                'excerpt' => $item['excerpt'],
                'content' => $item['content'],
                'robots'  => 'index,follow',
            ]],
            [],
            $exists !== null ? (int) $exists['project_id'] : null
        );
    }

    $projectsAdded++;
    echo '  + ' . $item['district'] . ' — ' . $item['title'] . "\n";
}

// --- Blog yazilari ----------------------------------------------------------

echo "\nBlog yazıları:\n";
$postsAdded = 0;
foreach (require ARC_ROOT . '/db/seed/posts.php' as $item) {
    $exists = $db->first(
        'SELECT post_id FROM post_translations WHERE lang = :lang AND slug = :slug',
        [':lang' => 'tr', ':slug' => $item['slug']]
    );

    if ($exists !== null && !$force) {
        continue;
    }

    if (!$dry) {
        // Kapak gorseli bilerek bos; isletme kendi fotografini Medya
        // ekranindan yukleyip yaziya bagliyor.
        Post::save(
            [
                'category'     => $item['category'],
                'status'       => 'published',
                'published_at' => $item['published_at'],
            ],
            ['tr' => [
                'title'            => $item['title'],
                'slug'             => $item['slug'],
                'excerpt'          => $item['excerpt'],
                'content'          => $item['content'],
                'meta_title'       => $item['meta_title'],
                'meta_description' => $item['meta_description'],
                'robots'           => 'index,follow',
            ]],
            $exists !== null ? (int) $exists['post_id'] : null
        );
    }

    $postsAdded++;
    echo '  + ' . $item['category'] . ' — ' . $item['title'] . "\n";
}

// --- SSS --------------------------------------------------------------------

echo "\nSSS kayıtları:\n";
$faqsAdded = 0;
foreach (require ARC_ROOT . '/db/seed/faqs.php' as $item) {
    $exists = $db->first(
        'SELECT faq_id FROM faq_translations WHERE lang = :lang AND question = :q',
        [':lang' => 'tr', ':q' => $item['q']]
    );

    if ($exists !== null && !$force) {
        continue;
    }

    // Atanacak sayfa kimliklerini slug'dan coz.
    $pageIds = [];
    foreach ($item['pages'] as $slug) {
        if (isset($slugToPage[$slug])) {
            $pageIds[] = $slugToPage[$slug];
            continue;
        }
        $row = $db->first(
            'SELECT page_id FROM page_translations WHERE lang = :lang AND slug = :slug',
            [':lang' => 'tr', ':slug' => $slug]
        );
        if ($row !== null) {
            $pageIds[] = (int) $row['page_id'];
        }
    }

    if (!$dry) {
        Faq::save(
            ['sort' => (int) $item['sort'], 'status' => 1],
            ['tr' => ['question' => $item['q'], 'answer' => $item['a']]],
            $pageIds,
            (bool) $item['home'],
            $exists !== null ? (int) $exists['faq_id'] : null
        );
    }

    $faqsAdded++;
    echo '  + ' . mb_substr($item['q'], 0, 60) . "\n";
}

// --- Ilce noktalarini sayfalara bagla ---------------------------------------

if (!$dry) {
    $linked = 0;
    foreach (Seeder::districtSeed() as $district) {
        $row = $db->first(
            'SELECT page_id FROM page_translations WHERE lang = :lang AND slug = :slug',
            [':lang' => 'tr', ':slug' => $district['slug']]
        );
        if ($row !== null) {
            $db->update('districts', ['page_id' => (int) $row['page_id']], ['name' => $district['name']]);
            $linked++;
        }
    }
    echo "\nBölge haritasında {$linked} ilçe noktası sayfasına bağlandı.\n";
}

// --- Menu -------------------------------------------------------------------

echo "\n";
echo $dry ? "Kuru çalışma; hiçbir kayıt yazılmadı.\n" : "Tamamlandı.\n";
printf("Sayfa: %d eklendi, %d atlandı. Örnek site: %d. Blog: %d. SSS: %d.\n", $added, $skipped, $projectsAdded, $postsAdded, $faqsAdded);

if (!$dry) {
    Logger::info('Başlangıç içeriği yazıldı', [
        'sayfa' => $added, 'ornek_site' => $projectsAdded,
        'blog' => $postsAdded, 'sss' => $faqsAdded,
    ]);
}

exit(0);
