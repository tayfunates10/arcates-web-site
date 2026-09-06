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

test('F-01', 'Sayfa olusturulup yayinlaninca on yuzde gorunur', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $id = arc_save_page([
        'type'   => 'page',
        'status' => 'published',
        'sort'   => 0,
        't'      => [
            'tr' => [
                'title'   => 'Hakkimizda',
                'slug'    => 'hakkimizda',
                'excerpt' => 'Edremit Korfezinde yazilim gelistiriyoruz.',
                'content' => '<h2>Kimiz</h2><p>Korfezde calisan kucuk bir ekibiz.</p>'
                    . '<p><a href="/iletisim">Bize yazin</a> veya <a href="/referanslar">calismalarimiza</a> bakin.</p>',
                'robots'  => 'index,follow',
            ],
        ],
    ]);

    assertGreaterThan(0, $id, 'Sayfa kaydedilmeli');

    $response = arc_visit('hakkimizda');
    assertSame(200, $response->status(), 'Yayindaki sayfa 200 dondurmeli');

    $body = $response->body();
    assertContains('<h1 class="page__title">Hakkimizda</h1>', $body, 'Baslik H1 olarak basilmali');
    assertContains('Korfezde calisan kucuk bir ekibiz', $body, 'Icerik gorunmeli');
    assertSame(1, substr_count($body, '<h1'), 'Sayfada tek H1 bulunmali');

    arc_logout_test();
});

test('F-02', 'Taslaga alinan sayfa on yuzde 404 doner', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $id = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Fiyatlar', 'slug' => 'fiyatlar', 'content' => '<p>Fiyat listesi.</p>']],
    ]);

    assertSame(200, arc_visit('fiyatlar')->status(), 'Once yayinda olmali');

    $db->update('pages', ['status' => 'draft'], ['id' => $id]);

    assertSame(404, arc_visit('fiyatlar')->status(), 'Taslak sayfa 404 dondurmeli');

    arc_logout_test();
});

test('F-03', 'Slug degisince eski adres 301 ile yeniye gider', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $id = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Web Tasarim', 'slug' => 'web-tasarim', 'content' => '<p>Hizmet.</p>']],
    ]);

    assertSame(200, arc_visit('web-tasarim')->status(), 'Ilk adres calismali');

    arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Web Tasarim', 'slug' => 'edremit-web-tasarim', 'content' => '<p>Hizmet.</p>']],
    ], $id);

    $redirect = Redirect::byFrom('/web-tasarim');
    assertTrue($redirect !== null, 'Otomatik yonlendirme kaydi olusmali');
    assertSame('/edremit-web-tasarim', $redirect['to_path'], 'Hedef yeni adres olmali');
    assertSame(301, (int) $redirect['code'], 'Kalici yonlendirme olmali');

    $response = (new Arcates\Controllers\Front\NotFoundController())
        ->handle(Request::make('GET', '/web-tasarim'));

    assertSame(301, $response->status(), 'Eski adres 301 dondurmeli');
    assertContains('/edremit-web-tasarim', (string) $response->headerLine('Location'), 'Yeni adrese gitmeli');

    assertSame(200, arc_visit('edremit-web-tasarim')->status(), 'Yeni adres calismali');

    arc_logout_test();
});

test('F-04', 'Ingilizce ceviri eklenince /en/slug calisir ve hreflang dogru olur', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => [
            'tr' => ['title' => 'Hakkimizda', 'slug' => 'hakkimizda', 'content' => '<p>Turkce icerik.</p>'],
            'en' => ['title' => 'About us', 'slug' => 'about-us', 'content' => '<p>English content.</p>'],
        ],
    ]);

    $en = arc_visit('about-us', 'en');
    assertSame(200, $en->status(), 'Ingilizce sayfa acilmali');
    assertContains('English content', $en->body());
    assertContains('lang="en"', $en->body(), 'HTML dili en olmali');

    $body = $en->body();
    // hreflang seti yalnizca <link rel="alternate"> ogeleriyle denetlenir;
    // dil degistiricideki baglantilar bu setin parcasi degildir.
    assertContains('rel="alternate" hreflang="tr"', $body, 'Turkce karsilik hreflang setinde olmali');
    assertContains('rel="alternate" hreflang="en"', $body, 'Ingilizce hreflang setinde olmali');
    assertContains('rel="alternate" hreflang="x-default"', $body, 'x-default bulunmali');
    assertNotContains('rel="alternate" hreflang="de"', $body, 'Cevirisi olmayan Almanca hreflang almamali');
    assertNotContains('rel="alternate" hreflang="ar"', $body, 'Cevirisi olmayan Arapca hreflang almamali');

    $tr = arc_visit('hakkimizda', 'tr');
    assertSame(200, $tr->status(), 'Turkce sayfa acilmali');
    assertContains('lang="tr"', $tr->body());

    Lang::use('tr');
    arc_logout_test();
});

test('F-05', 'Arapca sayfa rtl yonunde acilir', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => [
            'tr' => ['title' => 'Iletisim', 'slug' => 'iletisim', 'content' => '<p>Turkce.</p>'],
            'ar' => ['title' => 'اتصل بنا', 'slug' => 'ittisal', 'content' => '<p>محتوى عربي.</p>'],
        ],
    ]);

    $ar = arc_visit('ittisal', 'ar');
    assertSame(200, $ar->status(), 'Arapca sayfa acilmali');

    $body = $ar->body();
    assertContains('dir="rtl"', $body, 'Yon rtl olmali');
    assertContains('lang="ar"', $body, 'HTML dili ar olmali');
    assertContains('محتوى عربي', $body, 'Arapca icerik bozulmadan basilmali');

    Lang::use('tr');
    arc_logout_test();
});

test('U-06', 'Slug cakismasinda ikinci kayit -2 eki alir', function (): void {
    $db = arc_need_db();
    arc_clean_pages($db);
    arc_login_as($db, 'admin');

    $first = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Referanslar', 'slug' => 'referanslar', 'content' => '<p>Bir.</p>']],
    ]);

    $second = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Referanslar', 'slug' => 'referanslar', 'content' => '<p>Iki.</p>']],
    ]);

    assertGreaterThan(0, $second, 'Ikinci sayfa kaydedilmeli');
    assertNotSame($first, $second, 'Iki ayri kayit olusmali');

    $slug = (string) $db->value(
        'SELECT slug FROM page_translations WHERE page_id = :id AND lang = :lang',
        [':id' => $second, ':lang' => 'tr']
    );

    assertSame('referanslar-2', $slug, 'Ikinci kayit -2 eki almali');

    $third = arc_save_page([
        'type' => 'page', 'status' => 'published',
        't' => ['tr' => ['title' => 'Referanslar', 'slug' => 'referanslar', 'content' => '<p>Uc.</p>']],
    ]);

    $thirdSlug = (string) $db->value(
        'SELECT slug FROM page_translations WHERE page_id = :id AND lang = :lang',
        [':id' => $third, ':lang' => 'tr']
    );
    assertSame('referanslar-3', $thirdSlug, 'Ucuncu kayit -3 eki almali');

    arc_logout_test();
});

test('F-10', 'Menu sirasi degisince on yuze yansir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM menu_items');

    (new Arcates\Controllers\Admin\MenuController())->save(
        Request::make('POST', admin_url('menuler'), [
            '_token' => Security::csrfToken(),
            'menu'   => 'main',
            'items'  => [
                ['id' => 0, 'labels' => ['tr' => 'Iletisim'], 'url' => '/iletisim', 'sort' => 2, 'target' => '_self'],
                ['id' => 0, 'labels' => ['tr' => 'Hizmetler'], 'url' => '/web-tasarim', 'sort' => 1, 'target' => '_self'],
            ],
        ]),
        []
    );

    $tree = Arcates\Models\MenuItem::tree('main', 'tr');
    assertCount(2, $tree, 'Iki menu ogesi olmali');
    assertSame('Hizmetler', $tree[0]['label'], 'Sira 1 olan oge basta gelmeli');
    assertSame('Iletisim', $tree[1]['label']);

    // Sirayi ters cevir.
    $ids = $db->all('SELECT id, sort FROM menu_items ORDER BY sort');
    $db->update('menu_items', ['sort' => 9], ['id' => (int) $ids[0]['id']]);

    $reordered = Arcates\Models\MenuItem::tree('main', 'tr');
    assertSame('Iletisim', $reordered[0]['label'], 'Yeni sira on yuze yansimali');

    arc_logout_test();
    $db->run('DELETE FROM menu_items');
});
