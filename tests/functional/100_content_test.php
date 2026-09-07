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
            'client_name' => 'Korfez Otel',
            'sector'      => 'Turizm',
            'district'    => 'Akcay',
            'live_url'    => 'https://korfezotel.example',
            'status'      => 'published',
            'sort'        => 0,
        ], $project),
        ['tr' => array_merge([
            'title'   => 'Korfez Otel rezervasyon sitesi',
            'slug'    => 'korfez-otel',
            'excerpt' => 'Komisyonsuz dogrudan rezervasyon.',
            'content' => '<h2>Yapilan isler</h2><p>Rezervasyon motoru kuruldu.</p>',
            'robots'  => 'index,follow',
        ], $translation)]
    );
}

test('F-P10-a', 'Referans listesi ve detayi yayindakileri gosterir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    arc_project($db);
    arc_project($db, ['status' => 'draft', 'client_name' => 'Taslak Musteri'], ['title' => 'Taslak is', 'slug' => 'taslak-is']);

    $list = (new ProjectController())->index(Request::make('GET', '/referanslar'), []);
    assertSame(200, $list->status(), 'Liste acilmali');
    assertContains('Korfez Otel rezervasyon sitesi', $list->body(), 'Yayindaki referans gorunmeli');
    assertNotContains('Taslak is', $list->body(), 'Taslak referans gorunmemeli');

    $detail = (new ProjectController())->show(Request::make('GET', '/referanslar/korfez-otel'), ['slug' => 'korfez-otel']);
    assertSame(200, $detail->status(), 'Detay acilmali');

    $body = $detail->body();
    assertContains('Rezervasyon motoru kuruldu', $body, 'Yapilan isler gorunmeli');
    assertContains('Korfez Otel', $body, 'Musteri adi gorunmeli');
    assertContains('korfezotel.example', $body, 'Canli site linki gorunmeli');
    assertContains('rel="noopener nofollow"', $body, 'Dis baglanti nofollow olmali');
    assertContains('"CreativeWork"', $body, 'CreativeWork semasi bulunmali');
    assertSame(1, substr_count($body, '<h1'), 'Tek H1 bulunmali');

    // Taslak referans 404 doner.
    $draft = (new ProjectController())->show(Request::make('GET', '/referanslar/taslak-is'), ['slug' => 'taslak-is']);
    assertSame(404, $draft->status(), 'Taslak referans 404 dondurmeli');

    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM not_found');
});

test('F-P10-b', 'Ilce sayfasi o ilceye ait referansi gosterir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    arc_project($db, ['district' => 'Edremit'], ['title' => 'Edremit zeytinyagi magazasi', 'slug' => 'edremit-zeytinyagi']);

    arc_page($db, ['type' => 'location', 'template' => 'location', 'district' => 'Edremit'], [
        'title'   => 'Edremit Web Tasarim',
        'slug'    => 'edremit-web-tasarim',
        'content' => '<h2>Edremit</h2><p>Yerel isletmeler icin.</p>',
    ]);

    $body = arc_visit('edremit-web-tasarim')->body();

    assertContains('Edremit zeytinyagi magazasi', $body, 'Ilceye ait referans gorunmeli');
    assertContains('/referanslar/edremit-zeytinyagi', $body, 'Referansa baglanti verilmeli');

    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM pages');
});

test('F-P10-c', 'Ileri tarihli blog yazisi tarihi gelene kadar gorunmez', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    // Yayindaki yazi
    Post::save(
        ['category' => 'SEO', 'status' => 'published', 'published_at' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ['tr' => ['title' => 'Yerel SEO rehberi', 'slug' => 'yerel-seo-rehberi', 'content' => '<p>Icerik.</p>', 'robots' => 'index,follow']]
    );

    // Ileri tarihli yazi
    Post::save(
        ['category' => 'SEO', 'status' => 'published', 'published_at' => date('Y-m-d H:i:s', strtotime('+7 days'))],
        ['tr' => ['title' => 'Gelecek yazi', 'slug' => 'gelecek-yazi', 'content' => '<p>Henuz yok.</p>', 'robots' => 'index,follow']]
    );

    $list = (new PostController())->index(Request::make('GET', '/blog'), []);
    assertSame(200, $list->status(), 'Blog listesi acilmali');
    assertContains('Yerel SEO rehberi', $list->body(), 'Yayindaki yazi gorunmeli');
    assertNotContains('Gelecek yazi', $list->body(), 'Ileri tarihli yazi gorunmemeli');

    $future = (new PostController())->show(Request::make('GET', '/blog/gelecek-yazi'), ['slug' => 'gelecek-yazi']);
    assertSame(404, $future->status(), 'Ileri tarihli yazi 404 dondurmeli');

    $post = (new PostController())->show(Request::make('GET', '/blog/yerel-seo-rehberi'), ['slug' => 'yerel-seo-rehberi']);
    assertSame(200, $post->status(), 'Yayindaki yazi acilmali');
    assertContains('"Article"', $post->body(), 'Article semasi bulunmali');
    assertContains('datePublished', $post->body(), 'Yayin tarihi semada olmali');

    // Sitemap'e de girmemeli.
    $xml = arc_sitemap();
    assertContains('/blog/yerel-seo-rehberi', $xml, 'Yayindaki yazi haritada olmali');
    assertNotContains('/blog/gelecek-yazi', $xml, 'Ileri tarihli yazi haritada olmamali');

    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM not_found');
});

test('F-P10-d', 'SSS kaydi sayfaya atanir ve FAQPage semasina girer', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM faqs');
    $db->run('DELETE FROM pages');
    Lang::use('tr');

    $pageId = arc_page($db, ['type' => 'service', 'template' => 'service'], [
        'title'   => 'Web Tasarim',
        'slug'    => 'web-tasarim',
        'content' => '<h2>Hizmet</h2><p>Aciklama.</p>',
    ]);

    $faqId = Faq::save(
        ['sort' => 1, 'status' => 1],
        ['tr' => ['question' => 'Site ne kadar surede biter?', 'answer' => '<p>Ortalama uc hafta.</p>']],
        [$pageId],
        true
    );

    assertGreaterThan(0, $faqId, 'SSS kaydi olusmali');

    // Hizmet sayfasinda gorunmeli
    $body = arc_visit('web-tasarim')->body();
    assertContains('Site ne kadar surede biter?', $body, 'Soru sayfada gorunmeli');
    assertContains('Ortalama uc hafta', $body, 'Cevap sayfada gorunmeli');
    assertContains('"FAQPage"', $body, 'FAQPage semasi bulunmali');

    // Anasayfada da gorunmeli
    Arcates\Models\HomeSection::ensureDefaults();
    assertContains('Site ne kadar surede biter?', arc_home()->body(), 'Anasayfada gorunmeli');

    // /sss sayfasinda gorunmeli
    $faqPage = (new FaqController())->index(Request::make('GET', '/sss'), []);
    assertSame(200, $faqPage->status(), 'SSS sayfasi acilmali');
    assertContains('Site ne kadar surede biter?', $faqPage->body(), 'SSS listesinde gorunmeli');

    // Kapatilinca gorunmemeli
    $db->update('faqs', ['status' => 0], ['id' => $faqId]);
    assertNotContains('Site ne kadar surede biter?', arc_visit('web-tasarim')->body(), 'Kapali SSS gorunmemeli');

    $db->run('DELETE FROM faqs');
    $db->run('DELETE FROM pages');
});

test('F-P10-e', 'Panel referans ve blog ekranlari kayit olusturur', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM posts');

    $projectResponse = (new Arcates\Controllers\Admin\ProjectController())->store(
        Request::make('POST', admin_url('referanslar/yeni'), [
            '_token'      => Security::csrfToken(),
            'client_name' => 'Zeytin Kooperatifi',
            'sector'      => 'Zeytinyagi',
            'district'    => 'Burhaniye',
            'live_url'    => 'https://kooperatif.example',
            'status'      => 'published',
            't'           => ['tr' => ['title' => 'Kooperatif e-ticaret sitesi', 'content' => '<p>Magaza kuruldu.</p>']],
        ]),
        []
    );
    assertSame(302, $projectResponse->status(), 'Referans kaydedilmeli');

    $project = $db->first('SELECT * FROM projects ORDER BY id DESC LIMIT 1');
    assertSame('Zeytin Kooperatifi', $project['client_name']);
    assertSame('Burhaniye', $project['district']);

    $slug = (string) $db->value('SELECT slug FROM project_translations WHERE project_id = :id', [':id' => (int) $project['id']]);
    assertSame('kooperatif-e-ticaret-sitesi', $slug, 'Slug baslikten uretilmeli');

    $postResponse = (new Arcates\Controllers\Admin\PostController())->store(
        Request::make('POST', admin_url('blog/yeni'), [
            '_token'       => Security::csrfToken(),
            'category'     => 'Rehber',
            'status'       => 'published',
            'published_at' => date('Y-m-d\TH:i'),
            't'            => ['tr' => ['title' => 'Zeytinyagi satisi rehberi', 'content' => '<p>Rehber icerigi.</p>']],
        ]),
        []
    );
    assertSame(302, $postResponse->status(), 'Yazi kaydedilmeli');

    $post = $db->first('SELECT * FROM posts ORDER BY id DESC LIMIT 1');
    assertSame('Rehber', $post['category']);
    assertTrue($post['author_id'] !== null, 'Yazar kaydedilmeli');

    $db->run('DELETE FROM projects');
    $db->run('DELETE FROM posts');
    arc_logout_test();
});

test('F-P10-f', 'Blog slug degisiminde 301 olusur', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM redirects');

    $id = Post::save(
        ['category' => 'Rehber', 'status' => 'published', 'published_at' => date('Y-m-d H:i:s')],
        ['tr' => ['title' => 'Eski baslik', 'slug' => 'eski-baslik', 'content' => '<p>Icerik.</p>', 'robots' => 'index,follow']]
    );

    (new Arcates\Controllers\Admin\PostController())->store(
        Request::make('POST', admin_url('blog/' . $id), [
            '_token'       => Security::csrfToken(),
            'category'     => 'Rehber',
            'status'       => 'published',
            'published_at' => date('Y-m-d\TH:i'),
            't'            => ['tr' => ['title' => 'Yeni baslik', 'slug' => 'yeni-baslik', 'content' => '<p>Icerik.</p>']],
        ]),
        ['id' => $id]
    );

    $redirect = Arcates\Models\Redirect::byFrom('/blog/eski-baslik');
    assertTrue($redirect !== null, 'Yonlendirme olusmali');
    assertSame('/blog/yeni-baslik', $redirect['to_path'], 'Yeni adrese gitmeli');

    $db->run('DELETE FROM posts');
    $db->run('DELETE FROM redirects');
    arc_logout_test();
});
