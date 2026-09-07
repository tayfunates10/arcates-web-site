<?php
/**
 * Sayfa yasam dongusu.  DOCS.md 14.4 — testler F-01, F-02, F-03, F-04, F-05
 */

declare(strict_types=1);

use Arcates\Controllers\Admin\PageController as AdminPageController;
use Arcates\Controllers\Front\PageController as FrontPageController;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Models\Page;
use Arcates\Models\Redirect;

/** Panel formundan sayfa kaydeder ve kimligini dondurur. */
function arc_save_page(array $post, int $id = 0): int
{
    $post['_token'] = Security::csrfToken();

    $response = (new AdminPageController())->store(
        Request::make('POST', admin_url('sayfalar' . ($id > 0 ? '/' . $id : '/yeni')), $post),
        $id > 0 ? ['id' => $id] : []
    );

    if ($response->status() !== 302) {
        return 0;
    }

    $location = (string) $response->headerLine('Location');
    if (preg_match('#/sayfalar/(\d+)#', $location, $m) === 1) {
        return (int) $m[1];
    }

    return 0;
}

/** On yuzden sayfayi ister. */
function arc_visit(string $slug, string $lang = 'tr'): Arcates\Core\Response
{
    Lang::use($lang);
    return (new FrontPageController())->show(
        Request::make('GET', ($lang === 'tr' ? '' : '/' . $lang) . '/' . $slug),
        ['slug' => $slug]
    );
}

function arc_clean_pages(Database $db): void
{
    $db->run('DELETE FROM redirects');
    $db->run('DELETE FROM pages');
}

test('F-01', 'Sayfa oluşturulup yayınlanınca on yüzde görünür', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $id = arc_save_page([
        'type'   => 'page',
        'status' => 'published',
        'sort'   => 0,
        't'      => [
            'tr' => [
                'title'   => 'Hakkımızda',
                'slug'    => 'hakkimizda',
                'excerpt' => 'Edremit Körfezinde yazılım geliştiriyoruz.',
                'content' => '<h2>Kimiz</h2><p>Körfezde çalışan küçük bir ekibiz.</p>'
                    . '<p><a href="/iletisim">Bize yazın</a> veya <a href="/referanslar">çalışmalarımıza</a> bakın.</p>',
                'robots'  => 'index,follow',
            ],
        ],
    ]);

    assertGreaterThan(0, $id, 'Sayfa kaydedilmeli');

    $response = arc_visit('hakkimizda');
    assertSame(200, $response->status(), 'Yayındaki sayfa 200 döndürmeli');

    $body = $response->body();
    assertContains('<h1 class="page__title">Hakkımızda</h1>', $body, 'Başlık H1 olarak basılmalı');
    assertContains('Körfezde çalışan küçük bir ekibiz', $body, 'İçerik görünmeli');
    assertSame(1, substr_count($body, '<h1'), 'Sayfada tek H1 bulunmalı');

    arc_logout_test();
});

test('F-02', 'Taslağa alınan sayfa on yüzde 404 döner', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $id = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Fiyatlar', 'slug' => 'fiyatlar', 'content' => '<p>Fiyat listesi.</p>']],
    ]);

    assertSame(200, arc_visit('fiyatlar')->status(), 'Önce yayında olmalı');

    $db->update('pages', ['status' => 'draft'], ['id' => $id]);

    assertSame(404, arc_visit('fiyatlar')->status(), 'Taslak sayfa 404 döndürmeli');

    arc_logout_test();
});

test('F-03', 'Slug değişince eski adres 301 ile yeniye gider', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $id = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Web Tasarım', 'slug' => 'web-tasarim', 'content' => '<p>Hizmet.</p>']],
    ]);

    assertSame(200, arc_visit('web-tasarim')->status(), 'İlk adres çalışmalı');

    arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Web Tasarım', 'slug' => 'edremit-web-tasarim', 'content' => '<p>Hizmet.</p>']],
    ], $id);

    $redirect = Redirect::byFrom('/web-tasarim');
    assertTrue($redirect !== null, 'Otomatik yönlendirme kaydı oluşmalı');
    assertSame('/edremit-web-tasarim', $redirect['to_path'], 'Hedef yeni adres olmalı');
    assertSame(301, (int) $redirect['code'], 'Kalıcı yönlendirme olmalı');

    $response = (new Arcates\Controllers\Front\NotFoundController())
        ->handle(Request::make('GET', '/web-tasarim'));

    assertSame(301, $response->status(), 'Eski adres 301 döndürmeli');
    assertContains('/edremit-web-tasarim', (string) $response->headerLine('Location'), 'Yeni adrese gitmeli');

    assertSame(200, arc_visit('edremit-web-tasarim')->status(), 'Yeni adres çalışmalı');

    arc_logout_test();
});

test('F-04', 'İngilizce çeviri eklenince /en/slug çalışır ve hreflang doğru olur', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => [
            'tr' => ['title' => 'Hakkımızda', 'slug' => 'hakkimizda', 'content' => '<p>Türkçe içerik.</p>'],
            'en' => ['title' => 'About us', 'slug' => 'about-us', 'content' => '<p>English content.</p>'],
        ],
    ]);

    $en = arc_visit('about-us', 'en');
    assertSame(200, $en->status(), 'İngilizce sayfa açılmalı');
    assertContains('English content', $en->body());
    assertContains('lang="en"', $en->body(), 'HTML dili en olmalı');

    $body = $en->body();
    // hreflang seti yalnizca <link rel="alternate"> ogeleriyle denetlenir;
    // dil degistiricideki baglantilar bu setin parcasi degildir.
    assertContains('rel="alternate" hreflang="tr"', $body, 'Türkçe karşılık hreflang setinde olmalı');
    assertContains('rel="alternate" hreflang="en"', $body, 'İngilizce hreflang setinde olmalı');
    assertContains('rel="alternate" hreflang="x-default"', $body, 'x-default bulunmalı');
    assertNotContains('rel="alternate" hreflang="de"', $body, 'Çevirisi olmayan Almanca hreflang almamalı');
    assertNotContains('rel="alternate" hreflang="ar"', $body, 'Çevirisi olmayan Arapça hreflang almamalı');

    $tr = arc_visit('hakkimizda', 'tr');
    assertSame(200, $tr->status(), 'Türkçe sayfa açılmalı');
    assertContains('lang="tr"', $tr->body());

    Lang::use('tr');
    arc_logout_test();
});

test('F-05', 'Arapça sayfa rtl yönünde açılır', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => [
            'tr' => ['title' => 'İletişim', 'slug' => 'iletisim', 'content' => '<p>Türkçe.</p>'],
            'ar' => ['title' => 'اتصل بنا', 'slug' => 'ittisal', 'content' => '<p>محتوى عربي.</p>'],
        ],
    ]);

    $ar = arc_visit('ittisal', 'ar');
    assertSame(200, $ar->status(), 'Arapça sayfa açılmalı');

    $body = $ar->body();
    assertContains('dir="rtl"', $body, 'Yön rtl olmalı');
    assertContains('lang="ar"', $body, 'HTML dili ar olmalı');
    assertContains('محتوى عربي', $body, 'Arapça içerik bozulmadan basılmalı');

    Lang::use('tr');
    arc_logout_test();
});

test('U-06', 'Slug çakışmasında ikinci kayıt -2 eki alır', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $first = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Referanslar', 'slug' => 'referanslar', 'content' => '<p>Bir.</p>']],
    ]);

    $second = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Referanslar', 'slug' => 'referanslar', 'content' => '<p>İki.</p>']],
    ]);

    assertGreaterThan(0, $second, 'İkinci sayfa kaydedilmeli');
    assertNotSame($first, $second, 'İki ayrı kayıt oluşmalı');

    $slug = (string) $db->value(
        'SELECT slug FROM page_translations WHERE page_id = :id AND lang = :lang',
        [':id' => $second, ':lang' => 'tr']
    );

    assertSame('referanslar-2', $slug, 'İkinci kayıt -2 eki almalı');

    $third = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Referanslar', 'slug' => 'referanslar', 'content' => '<p>Üç.</p>']],
    ]);

    $thirdSlug = (string) $db->value(
        'SELECT slug FROM page_translations WHERE page_id = :id AND lang = :lang',
        [':id' => $third, ':lang' => 'tr']
    );
    assertSame('referanslar-3', $thirdSlug, 'Üçüncü kayıt -3 eki almalı');

    arc_logout_test();
});

test('F-10', 'Menü sırası değişince on yüze yansır', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM menu_items');

    (new Arcates\Controllers\Admin\MenuController())->save(
        Request::make('POST', admin_url('menuler'), [
            '_token' => Security::csrfToken(),
            'menu'   => 'main',
            'items'  => [
                ['id' => 0, 'labels' => ['tr' => 'İletişim'], 'url' => '/iletisim', 'sort' => 2, 'target' => '_self'],
                ['id' => 0, 'labels' => ['tr' => 'Hizmetler'], 'url' => '/web-tasarim', 'sort' => 1, 'target' => '_self'],
            ],
        ]),
        []
    );

    $tree = Arcates\Models\MenuItem::tree('main', 'tr');
    assertCount(2, $tree, 'İki menü ögesi olmalı');
    assertSame('Hizmetler', $tree[0]['label'], 'Sıra 1 olan öge başta gelmeli');
    assertSame('İletişim', $tree[1]['label']);

    // Sirayi ters cevir.
    $ids = $db->all('SELECT id, sort FROM menu_items ORDER BY sort');
    $db->update('menu_items', ['sort' => 9], ['id' => (int) $ids[0]['id']]);

    $reordered = Arcates\Models\MenuItem::tree('main', 'tr');
    assertSame('İletişim', $reordered[0]['label'], 'Yeni sıra on yüze yansımalı');

    arc_logout_test();
    $db->run('DELETE FROM menu_items');
});
