<?php
/**
 * Anasayfa bolum yoneticisi ve ilce verisi paneli.
 */
declare(strict_types=1);

use Arcates\Controllers\Admin\HomeController as AdminHomeController;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Models\District;
use Arcates\Models\HomeSection;

test('F-14b', 'Panelden gorunen bir bolum kapatilinca referans anasayfada kaybolur', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    assertContains('ref-services', arc_home()->body(), 'Hizmetler once gorunmeli');

    $response = (new AdminHomeController())->toggle(
        Request::make('POST', admin_url('anasayfa/services/durum'), ['_token' => Security::csrfToken()]),
        ['key' => 'services']
    );
    assertSame(302, $response->status(), 'Yonlendirme donmeli');
    assertNotContains('ref-services', arc_home()->body(), 'Kapali bolum on yuzde basilmamali');

    (new AdminHomeController())->toggle(
        Request::make('POST', admin_url('anasayfa/services/durum'), ['_token' => Security::csrfToken()]),
        ['key' => 'services']
    );
    assertContains('ref-services', arc_home()->body(), 'Yeniden acilinca gorunmeli');

    arc_logout_test();
});

test('F-15b', 'Panelden kaydedilen kahraman icerigi referans hero alanina yansir', function (): void {
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
                    'line1'       => 'Birinci satır',
                    'line2'       => 'İkinci satır',
                    'line3'       => 'Üçüncü satır degrade',
                    'description' => 'Panelden girilen açıklama metni.',
                    'cta1'        => ['label' => 'Teklif alın', 'url' => '/iletisim'],
                    'cta2'        => ['label' => 'Çalışmalar', 'url' => '/referanslar'],
                ],
            ],
        ]),
        ['key' => 'hero']
    );

    assertSame(302, $response->status(), 'Yonlendirme donmeli');

    $body = arc_home()->body();
    assertContains('Panelden girilen rozet', $body, 'Referanstaki hero ust etiketi panel verisini kullanmali');
    assertContains('Üçüncü satır degrade', $body, 'Ucuncu satir yansimali');
    assertContains('Panelden girilen açıklama metni.', $body, 'Aciklama yansimali');
    assertContains('Teklif alın', $body, 'Buton metni yansimali');
    assertContains('ref-accent', $body, 'Son hero satiri vurgu stilini korumali');

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
                    'title' => '<script>alert(1)</script>Başlık',
                    'text'  => 'Metin',
                    'cta1'  => ['label' => 'Tıkla', 'url' => 'javascript:alert(1)'],
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

test('F-16b', 'Ilce noktalari panelden kaydedilir ve veri katmaninda dogru URL ile cozulur', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    $db->run('DELETE FROM districts');
    $db->run('DELETE FROM pages');

    $pageId = $db->insert('pages', [
        'type' => 'location', 'template' => 'location', 'status' => 'published', 'district' => 'Havran',
    ]);
    $db->insert('page_translations', [
        'page_id' => $pageId, 'lang' => 'tr', 'title' => 'Havran Web Tasarım', 'slug' => 'havran-web-tasarim',
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

    $districts = District::forMap('tr');
    assertSame(1, count($districts), 'Etkin ilce veri katmaninda bulunmali');
    assertSame('Havran', $districts[0]['name']);
    assertSame(312, $districts[0]['map_x']);
    assertSame(104, $districts[0]['map_y']);
    assertContains('/havran-web-tasarim', $districts[0]['url'], 'Yayinlanmis sayfa URLsi cozulmeli');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});

test('F-16c', 'Sinir disi konum degerleri reddedilir', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM districts');

    (new AdminHomeController())->saveDistricts(
        Request::make('POST', admin_url('anasayfa/coast/ilceler'), [
            '_token'    => Security::csrfToken(),
            'districts' => [
                ['id' => 0, 'name' => 'Sınır', 'map_x' => '4000', 'map_y' => '900', 'sort' => '1', 'is_active' => '1'],
            ],
        ]),
        []
    );

    $saved = $db->first('SELECT * FROM districts WHERE name = :name', [':name' => 'Sınır']);
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
