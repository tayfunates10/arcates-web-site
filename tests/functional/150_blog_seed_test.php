<?php
/**
 * Blog baslangic yazilari ve marka varliklari.
 * DOCS.md 4.1 (URL haritasi), 9.4 (blog), 11.2
 */

declare(strict_types=1);

use Arcates\Controllers\Front\PostController;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Security;

/** Blog tohumunu yukler. */
function arc_post_seed(): array
{
    return require ARC_ROOT . '/db/seed/posts.php';
}

test('F-P15-a', 'Tohumda blog yazıları var ve her biri 300+ kelime', function (): void {
    $posts = arc_post_seed();
    assertTrue(count($posts) >= 5, 'En az beş yazı olmalı — bulunan: ' . count($posts));

    $slugs = [];
    foreach ($posts as $post) {
        $slug = (string) $post['slug'];
        assertTrue(preg_match('/^[a-z0-9-]+$/', $slug) === 1, "Slug ASCII olmalı: {$slug}");
        assertTrue(!isset($slugs[$slug]), "Slug tekrarlanmamalı: {$slug}");
        $slugs[$slug] = true;

        $words = Security::wordCount((string) $post['content']);
        assertTrue($words >= 300, "{$slug} yalnızca {$words} kelime; en az 300 gerekir");
    }
});

test('F-P15-b', 'Her yazının kategorisi, özeti ve meta alanları dolu', function (): void {
    foreach (arc_post_seed() as $post) {
        $slug = (string) $post['slug'];
        foreach (['category', 'title', 'excerpt', 'meta_title', 'meta_description', 'published_at'] as $field) {
            assertTrue(trim((string) ($post[$field] ?? '')) !== '', "{$slug}: {$field} boş olmamalı");
        }

        assertTrue(mb_strlen((string) $post['meta_description'], 'UTF-8') <= 320, "{$slug}: meta açıklama 320 karakteri aşmamalı");
        assertTrue(mb_strlen((string) $post['excerpt'], 'UTF-8') <= 400, "{$slug}: özet 400 karakteri aşmamalı");
        assertTrue(strtotime((string) $post['published_at']) !== false, "{$slug}: yayın tarihi geçerli olmalı");

        // Kapak gorseli bilerek bos; isletme kendi fotografini yukleyecek.
        assertTrue(!isset($post['cover_id']), "{$slug}: kapak görseli tohumda tanımlanmamalı");
    }
});

test('F-P15-c', 'Yazılar iç link taşır ve zararlı işaretleme içermez', function (): void {
    foreach (arc_post_seed() as $post) {
        $slug    = (string) $post['slug'];
        $content = (string) $post['content'];

        assertContains('href="/', $content, "{$slug}: en az bir iç link olmalı");
        assertNotContains('<script', $content, "{$slug}: script olmamalı");
        assertNotContains('javascript:', $content, "{$slug}: javascript şeması olmamalı");
        assertTrue(
            preg_match('/<[^>]+\\son[a-z]+\\s*=/i', $content) !== 1,
            "{$slug}: satır içi olay özniteliği olmamalı"
        );
    }
});

test('F-P15-d', 'Blog listesi ve yazı sayfası tohum içeriğini gösterir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM posts');
    Lang::use('tr');

    $seed = arc_post_seed()[0];
    \Arcates\Models\Post::save(
        ['category' => $seed['category'], 'status' => 'published', 'published_at' => $seed['published_at']],
        ['tr' => [
            'title'   => $seed['title'],
            'slug'    => $seed['slug'],
            'excerpt' => $seed['excerpt'],
            'content' => $seed['content'],
            'robots'  => 'index,follow',
        ]]
    );

    $list = (new PostController())->index(Request::make('GET', '/blog'), []);
    assertSame(200, $list->status(), 'Blog listesi açılmalı');
    assertContains($seed['title'], $list->body(), 'Yazı listede görünmeli');
    assertContains($seed['category'], $list->body(), 'Kategori süzgeci görünmeli');

    $detail = (new PostController())->show(Request::make('GET', '/blog/' . $seed['slug']), ['slug' => $seed['slug']]);
    assertSame(200, $detail->status(), 'Yazı sayfası açılmalı');
    assertContains('İşletme Profili', $detail->body(), 'Yazı gövdesi görünmeli');
});

test('F-P15-e', 'Marka görselleri yerinde ve şablonlar onları gösteriyor', function (): void {
    $img = ARC_ROOT . '/public/assets/img/';
    foreach (['logo-mark.png', 'logo-wordmark.png', 'favicon-32.png', 'apple-touch-icon.png', 'og-default.png'] as $file) {
        assertTrue(is_file($img . $file), "Marka görseli bulunmalı: {$file}");
        $size = getimagesize($img . $file);
        assertTrue($size !== false && $size[0] > 0, "Geçerli bir görsel olmalı: {$file}");
    }

    $og = getimagesize($img . 'og-default.png');
    assertSame(1200, $og[0], 'OG görseli 1200 piksel geniş olmalı');
    assertSame(630, $og[1], 'OG görseli 630 piksel yüksek olmalı');

    // Eski yer tutucu favicon hicbir sablonda kalmamali.
    foreach (['views/front/partials/head.php', 'views/admin/layout.php', 'views/errors/404.php'] as $rel) {
        $src = (string) file_get_contents(ARC_ROOT . '/' . $rel);
        assertNotContains('favicon.svg', $src, "{$rel}: eski favicon referansı kalmamalı");
        assertContains('favicon-32.png', $src, "{$rel}: yeni favicon bağlanmalı");
    }

    assertContains('logo-wordmark.png', (string) file_get_contents(ARC_ROOT . '/views/front/partials/footer.php'), 'Alt bilgide yazılı logo olmalı');
    assertContains('logo-mark.png', (string) file_get_contents(ARC_ROOT . '/public/assets/css/site.css'), 'Üst menü işareti logoyu kullanmalı');
});
