<?php
/**
 * Anasayfa bolum yoneticisi ve ilce haritasi paneli.
 * DOCS.md 9.2 — testler F-14, F-15, F-16
 */

declare(strict_types=1);

use Arcates\Controllers\Admin\HomeController as AdminHomeController;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Models\District;
use Arcates\Models\HomeSection;

test('F-14b', 'Panelden bolum kapatilinca on yuzde gorunmez', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    assertContains('class="strip"', arc_home()->body(), 'Serit once gorunmeli');

    $response = (new AdminHomeController())->toggle(
        Request::make('POST', admin_url('anasayfa/strip/durum'), ['_token' => Security::csrfToken()]),
        ['key' => 'strip']
    );
    assertSame(302, $response->status(), 'Yonlendirme donmeli');

    assertNotContains('class="strip"', arc_home()->body(), 'Kapali bolum on yuzde basilmamali');

    // Geri ac.
    (new AdminHomeController())->toggle(
        Request::make('POST', admin_url('anasayfa/strip/durum'), ['_token' => Security::csrfToken()]),
        ['key' => 'strip']
    );
    assertContains('class="strip"', arc_home()->body(), 'Yeniden acilinca gorunmeli');

    arc_logout_test();
});

test('F-15b', 'Panelden kaydedilen kahraman metni on yuze yansir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    $response = (new AdminHomeController())->update(
        Request::make('POST', admin_url('anasayfa/hero'), [
            '_token'    => Security::csrfToken(),
            'is_active' => '1',
            'c'         => [
                'tr' => [
                    'badge'       => 'Panelden girilen rozet',
                    'line1'       => 'Birinci satir',
                    'line2'       => 'Ikinci satir',
                    'line3'       => 'Ucuncu satir degrade',
                    'description' => 'Panelden girilen aciklama metni.',
                    'cta1'        => ['label' => 'Teklif alin', 'url' => '/iletisim'],
                    'cta2'        => ['label' => 'Calismalar', 'url' => '/referanslar'],
                ],
            ],
        ]),
        ['key' => 'hero']
    );

    assertSame(302, $response->status(), 'Yonlendirme donmeli');

    $body = arc_home()->body();
    assertContains('Panelden girilen rozet', $body, 'Rozet yansimali');
    assertContains('Ucuncu satir degrade', $body, 'Ucuncu satir yansimali');
    assertContains('Panelden girilen aciklama metni.', $body, 'Aciklama yansimali');
    assertContains('Teklif alin', $body, 'Buton metni yansimali');

    // Sablona sabit metin gomulmedigini dogrula: eski varsayilan artik yok.
    assertNotContains('Korfezdeki isletmeler icin', $body, 'Eski metin sablonda kalmamali');

    arc_logout_test();
});

test('F-15c', 'Bolum icerigi zararli girdiyle kirlenmez', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    (new AdminHomeController())->update(
        Request::make('POST', admin_url('anasayfa/cta'), [
            '_token'    => Security::csrfToken(),
            'is_active' => '1',
            'c'         => [
                'tr' => [
                    'title' => '<script>alert(1)</script>Baslik',
                    'text'  => 'Metin',
                    'cta1'  => ['label' => 'Tikla', 'url' => 'javascript:alert(1)'],
                ],
            ],
        ]),
        ['key' => 'cta']
    );

    $body = arc_home()->body();
    assertNotContains('<script>alert(1)</script>', $body, 'Script ham gecmemeli');
    assertContains('&lt;script&gt;', $body, 'Metin olarak kacirilmali');
    assertNotContains('href="javascript:', $body, 'javascript: adresi basilmamali');

    arc_logout_test();
});

test('F-16b', 'Ilce noktalari panelden konumlandirilir ve haritaya yansir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM districts');
    $db->run('DELETE FROM pages');

    $pageId = $db->insert('pages', [
        'type' => 'location', 'template' => 'location', 'status' => 'published', 'district' => 'Havran',
    ]);
    $db->insert('page_translations', [
        'page_id' => $pageId, 'lang' => 'tr', 'title' => 'Havran Web Tasarim', 'slug' => 'havran-web-tasarim',
    ]);

    $response = (new AdminHomeController())->saveDistricts(
        Request::make('POST', admin_url('anasayfa/coast/ilceler'), [
            '_token'    => Security::csrfToken(),
            'districts' => [
                [
                    'id' => 0, 'name' => 'Havran', 'map_x' => '312', 'map_y' => '104',
                    'label_above' => '1', 'page_id' => (string) $pageId, 'sort' => '1', 'is_active' => '1',
                ],
            ],
        ]),
        []
    );

    assertSame(302, $response->status(), 'Yonlendirme donmeli');

    $saved = $db->first('SELECT * FROM districts WHERE name = :name', [':name' => 'Havran']);
    assertTrue($saved !== null, 'Nokta kaydedilmeli');
    assertSame(312, (int) $saved['map_x'], 'Yatay konum kaydedilmeli');
    assertSame(104, (int) $saved['map_y'], 'Dikey konum kaydedilmeli');
    assertSame($pageId, (int) $saved['page_id'], 'Sayfa baglantisi kaydedilmeli');

    $figure = arc_extract_coast(arc_home()->body());
    assertContains('<circle cx="312" cy="104"', $figure, 'Nokta haritada dogru konumda olmali');
    assertContains('/havran-web-tasarim', $figure, 'Nokta sayfaya baglanmali');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});

test('F-16c', 'Sinir disi konum degerleri kirpilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM districts');

    (new AdminHomeController())->saveDistricts(
        Request::make('POST', admin_url('anasayfa/coast/ilceler'), [
            '_token'    => Security::csrfToken(),
            'districts' => [
                ['id' => 0, 'name' => 'Sinir', 'map_x' => '4000', 'map_y' => '900', 'sort' => '1', 'is_active' => '1'],
            ],
        ]),
        []
    );

    // Dogrulama sinir disi degeri reddeder; kayit olusmaz.
    $saved = $db->first('SELECT * FROM districts WHERE name = :name', [':name' => 'Sinir']);
    assertSame(null, $saved, 'Sinir disi deger kabul edilmemeli');

    arc_logout_test();
});

test('F-P6-a', 'Bolum listesi tum bolumleri sabit sirada gosterir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    $response = (new AdminHomeController())->index(Request::make('GET', admin_url('anasayfa')), []);
    assertSame(200, $response->status(), 'Liste acilmali');

    $body = $response->body();
    foreach (HomeSection::LABELS as $key => $label) {
        assertContains('<code>' . $key . '</code>', $body, "Bolum listede olmali: {$key}");
    }

    // Sira sartnamedeki gibi
    $positions = [];
    foreach (array_keys(HomeSection::LABELS) as $key) {
        $positions[] = (int) strpos($body, '<code>' . $key . '</code>');
    }
    $sorted = $positions;
    sort($sorted);
    assertSame($sorted, $positions, 'Bolumler sabit sirada listelenmeli');

    arc_logout_test();
});

test('F-P6-b', 'Editor rolu anasayfa bolumlerini duzenleyebilir', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'editor');

    $response = (new AdminHomeController())->index(Request::make('GET', admin_url('anasayfa')), []);
    assertSame(200, $response->status(), 'Editor anasayfa yoneticisini gormeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});
