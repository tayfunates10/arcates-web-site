<?php
/**
 * Referans, blog ve SSS modulleri.  DOCS.md 9.4, 4.1, 11.2
 */

declare(strict_types=1);

use Arcates\Controllers\Front\FaqController;
use Arcates\Controllers\Front\PostController;
use Arcates\Controllers\Front\ProjectController;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Models\Faq;
use Arcates\Models\Post;
use Arcates\Models\Project;

/** Yayindaki bir referans olusturur. */
function arc_project(Database $db, array $project = [], array $translation = []): int
{
    return Project::save(
        array_merge([
            'client_name' => 'Körfez Otel',
            'sector'      => 'Turizm',
            'district'    => 'Akçay',
            'live_url'    => 'https://korfezotel.example',
            'status'      => 'published',
            'sort'        => 0,
        ], $project),
        ['tr' => array_merge([
            'title'   => 'Körfez Otel rezervasyon sitesi',
            'slug'    => 'korfez-otel',
            'excerpt' => 'Komisyonsuz doğrudan rezervasyon.',
            'content' => '<h2>Yapılan işler</h2><p>Rezervasyon motoru kuruldu.</p>',
            'robots'  => 'index,follow',
        ], $translation)]
    );
}

test('F-P10-a', 'Referans listesi ve detayı yayındakileri gösterir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    arc_project($db);
    arc_project($db, ['status' => 'draft', 'client_name' => 'Taslak Müşteri'], ['title' => 'Taslak iş', 'slug' => 'taslak-is']);

    $list = (new ProjectController())->index(Request::make('GET', '/referanslar'), []);
    assertSame(200, $list->status(), 'Liste açılmalı');
    assertContains('Körfez Otel rezervasyon sitesi', $list->body(), 'Yayındaki referans görünmeli');
    assertNotContains('Taslak iş', $list->body(), 'Taslak referans görünmemeli');

    $detail = (new ProjectController())->show(Request::make('GET', '/referanslar/korfez-otel'), ['slug' => 'korfez-otel']);
    assertSame(200, $detail->status(), 'Detay açılmalı');

    $body = $detail->body();
    assertContains('Rezervasyon motoru kuruldu', $body, 'Yapılan işler görünmeli');
    assertContains('Körfez Otel', $body, 'Müşteri adı görünmeli');
    assertContains('korfezotel.example', $body, 'Canlı site linki görünmeli');
    assertContains('rel="noopener nofollow"', $body, 'Dış bağlantı nofollow olmalı');
    assertContains('"CreativeWork"', $body, 'CreativeWork şeması bulunmalı');
    assertSame(1, substr_count($body, '<h1'), 'Tek H1 bulunmalı');

    $draft = (new ProjectController())->show(Request::make('GET', '/referanslar/taslak-is'), ['slug' => 'taslak-is']);
    assertSame(404, $draft->status(), 'Taslak referans 404 döndürmeli');

    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM not_found');
});

test('F-P10-b', 'İlçe sayfası o ilçeye ait referansı gösterir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    arc_project($db, ['district' => 'Edremit'], ['title' => 'Edremit zeytinyağı mağazası', 'slug' => 'edremit-zeytinyagi']);

    arc_page($db, ['type' => 'location', 'template' => 'location', 'district' => 'Edremit'], [
        'title'   => 'Edremit Web Tasarım',
        'slug'    => 'edremit-web-tasarim',
        'content' => '<h2>Edremit</h2><p>Yerel işletmeler için.</p>',
    ]);

    $body = arc_visit('edremit-web-tasarim')->body();

    assertContains('Edremit zeytinyağı mağazası', $body, 'İlçeye ait referans görünmeli');
    assertContains('/referanslar/edremit-zeytinyagi', $body, 'Referansa bağlantı verilmeli');

    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM pages');
});

test('F-P10-c', 'İleri tarihli blog yazısı tarihi gelene kadar görünmez', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    Post::save(
        ['category' => 'SEO', 'status' => 'published', 'published_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['tr' => ['title' => 'Yerel SEO rehberi', 'slug' => 'yerel-seo-rehberi', 'content' => '<p>İçerik.</p>', 'robots' => 'index,follow']]
    );

    Post::save(
        ['category' => 'SEO', 'status' => 'published', 'published_at' => date('Y-m-d H:i:s', strtotime('+7 days'))],
        ['tr' => ['title' => 'Gelecek yazı', 'slug' => 'gelecek-yazi', 'content' => '<p>Henüz yok.</p>', 'robots' => 'index,follow']]
    );

    $list = (new PostController())->index(Request::make('GET', '/blog'), []);
    assertSame(200, $list->status(), 'Blog listesi açılmalı');
    assertContains('Yerel SEO rehberi', $list->body(), 'Yayındaki yazı görünmeli');
    assertNotContains('Gelecek yazı', $list->body(), 'İleri tarihli yazı görünmemeli');

    $future = (new PostController())->show(Request::make('GET', '/blog/gelecek-yazi'), ['slug' => 'gelecek-yazi']);
    assertSame(404, $future->status(), 'İleri tarihli yazı 404 döndürmeli');

    $post = (new PostController())->show(Request::make('GET', '/blog/yerel-seo-rehberi'), ['slug' => 'yerel-seo-rehberi']);
    assertSame(200, $post->status(), 'Yayındaki yazı açılmalı');
    assertContains('"Article"', $post->body(), 'Article şeması bulunmalı');
    assertContains('datePublished', $post->body(), 'Yayın tarihi şemada olmalı');

    $xml = arc_sitemap();
    assertContains('/blog/yerel-seo-rehberi', $xml, 'Yayındaki yazı haritada olmalı');
    assertNotContains('/blog/gelecek-yazi', $xml, 'İleri tarihli yazı haritada olmamalı');

    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM not_found');
});

test('F-P10-d', 'SSS kaydı hizmet ve SSS sayfasına atanır; referans anasayfa gizli FAQ üretmez', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM faqs');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    $pageId = arc_page($db, ['type' => 'service', 'template' => 'service'], [
        'title'   => 'Web Tasarım',
        'slug'    => 'web-tasarim',
        'content' => '<h2>Hizmet</h2><p>Açıklama.</p>',
    ]);

    $faqId = Faq::save(
        ['sort' => 1, 'status' => 1],
        ['tr' => ['question' => 'Site ne kadar sürede biter?', 'answer' => '<p>Ortalama üç hafta.</p>']],
        [$pageId],
        true
    );

    assertGreaterThan(0, $faqId, 'SSS kaydı oluşmalı');

    $body = arc_visit('web-tasarim')->body();
    assertContains('Site ne kadar sürede biter?', $body, 'Soru sayfada görünmeli');
    assertContains('Ortalama üç hafta', $body, 'Cevap sayfada görünmeli');
    assertContains('"FAQPage"', $body, 'FAQPage şeması bulunmalı');

    Arcates\Models\HomeSection::ensureDefaults();
    $homeBody = arc_home()->body();
    assertNotContains('Site ne kadar sürede biter?', $homeBody, 'Referans anasayfa görünmeyen SSS metni basmamalı');
    assertNotContains('"FAQPage"', $homeBody, 'Referans anasayfa görünmeyen FAQPage şeması basmamalı');

    $faqPage = (new FaqController())->index(Request::make('GET', '/sss'), []);
    assertSame(200, $faqPage->status(), 'SSS sayfası açılmalı');
    assertContains('Site ne kadar sürede biter?', $faqPage->body(), 'SSS listesinde görünmeli');

    $db->update('faqs', ['status' => 0], ['id' => $faqId]);
    assertNotContains('Site ne kadar sürede biter?', arc_visit('web-tasarim')->body(), 'Kapalı SSS görünmemeli');

    $db->run('DELETE FROM faqs');
    $db->run('DELETE FROM pages');
});

test('F-P10-e', 'Panel referans ve blog ekranları kayıt oluşturur', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM posts');

    $projectResponse = (new Arcates\Controllers\Admin\ProjectController())->store(
        Request::make('POST', admin_url('referanslar/yeni'), [
            '_token'      => Security::csrfToken(),
            'client_name' => 'Zeytin Kooperatifi',
            'sector'      => 'Zeytinyağı',
            'district'    => 'Burhaniye',
            'live_url'    => 'https://kooperatif.example',
            'status'      => 'published',
            't'           => ['tr' => ['title' => 'Kooperatif e-ticaret sitesi', 'content' => '<p>Mağaza kuruldu.</p>']],
        ]),
        []
    );
    assertSame(302, $projectResponse->status(), 'Referans kaydedilmeli');

    $project = $db->first('SELECT * FROM projects ORDER BY id DESC LIMIT 1');
    assertSame('Zeytin Kooperatifi', $project['client_name']);
    assertSame('Burhaniye', $project['district']);

    $slug = (string) $db->value('SELECT slug FROM project_translations WHERE project_id = :id', [':id' => (int) $project['id']]);
    assertSame('kooperatif-e-ticaret-sitesi', $slug, 'Slug başlıktan üretilmeli');

    $postResponse = (new Arcates\Controllers\Admin\PostController())->store(
        Request::make('POST', admin_url('blog/yeni'), [
            '_token'       => Security::csrfToken(),
            'category'     => 'Rehber',
            'status'       => 'published',
            'published_at' => date('Y-m-d\TH:i'),
            't'            => ['tr' => ['title' => 'Zeytinyağı satışı rehberi', 'content' => '<p>Rehber içeriği.</p>']],
        ]),
        []
    );
    assertSame(302, $postResponse->status(), 'Yazı kaydedilmeli');

    $post = $db->first('SELECT * FROM posts ORDER BY id DESC LIMIT 1');
    assertSame('Rehber', $post['category']);
    assertTrue($post['author_id'] !== null, 'Yazar kaydedilmeli');

    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM posts');
    arc_logout_test();
});

test('F-P10-f', 'Blog slug değişiminde 301 oluşur', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM redirects');

    $id = Post::save(
        ['category' => 'Rehber', 'status' => 'published', 'published_at' => date('Y-m-d H:i:s')],
        ['tr' => ['title' => 'Eski başlık', 'slug' => 'eski-baslik', 'content' => '<p>İçerik.</p>', 'robots' => 'index,follow']]
    );

    (new Arcates\Controllers\Admin\PostController())->store(
        Request::make('POST', admin_url('blog/' . $id), [
            '_token'       => Security::csrfToken(),
            'category'     => 'Rehber',
            'status'       => 'published',
            'published_at' => date('Y-m-d\TH:i'),
            't'            => ['tr' => ['title' => 'Yeni başlık', 'slug' => 'yeni-baslik', 'content' => '<p>İçerik.</p>']],
        ]),
        ['id' => $id]
    );

    $redirect = Arcates\Models\Redirect::byFrom('/blog/eski-baslik');
    assertTrue($redirect !== null, 'Yönlendirme oluşmalı');
    assertSame('/blog/yeni-baslik', $redirect['to_path'], 'Yeni adrese gitmeli');

    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM redirects');
    arc_logout_test();
});
