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

test('F-14b', 'Panelden bölüm kapatılınca on yüzde görünmez', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    assertContains('class="strip"', arc_home()->body(), 'Şerit önce görünmeli');

    $response = (new AdminHomeController())->toggle(
        Request::make('POST', admin_url('anasayfa/strip/durum'), ['_token' => Security::csrfToken()]),
        ['key' => 'strip']
    );
    assertSame(302, $response->status(), 'Yönlendirme dönmeli');

    assertNotContains('class="strip"', arc_home()->body(), 'Kapalı bölüm on yüzde basılmamalı');

    // Geri ac.
    (new AdminHomeController())->toggle(
        Request::make('POST', admin_url('anasayfa/strip/durum'), ['_token' => Security::csrfToken()]),
        ['key' => 'strip']
    );
    assertContains('class="strip"', arc_home()->body(), 'Yeniden açılınca görünmeli');

    arc_logout_test();
});

test('F-15b', 'Panelden kaydedilen kahraman metni on yüze yansır', function (): void {
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

    assertSame(302, $response->status(), 'Yönlendirme dönmeli');

    $body = arc_home()->body();
    assertContains('Panelden girilen rozet', $body, 'Rozet yansımalı');
    assertContains('Üçüncü satır degrade', $body, 'Üçüncü satır yansımalı');
    assertContains('Panelden girilen açıklama metni.', $body, 'Açıklama yansımalı');
    assertContains('Teklif alın', $body, 'Buton metni yansımalı');

    // Sablona sabit metin gomulmedigini dogrula: eski varsayilan artik yok.
    assertNotContains('Körfezdeki işletmeler için', $body, 'Eski metin şablonda kalmamalı');

    arc_logout_test();
});

test('F-15c', 'Bölüm içeriği zararlı girdiyle kirlenmez', function (): void {
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
    assertNotContains('<script>alert(1)</script>', $body, 'Script ham geçmemeli');
    assertContains('&lt;script&gt;', $body, 'Metin olarak kaçırılmalı');
    assertNotContains('href="javascript:', $body, 'javascript: adresi basılmamalı');

    arc_logout_test();
});

test('F-16b', 'İlçe noktaları panelden konumlandırılır ve haritaya yansır', function (): void {
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

    assertSame(302, $response->status(), 'Yönlendirme dönmeli');

    $saved = $db->first('SELECT * FROM districts WHERE name = :name', [':name' => 'Havran']);
    assertTrue($saved !== null, 'Nokta kaydedilmeli');
    assertSame(312, (int) $saved['map_x'], 'Yatay konum kaydedilmeli');
    assertSame(104, (int) $saved['map_y'], 'Dikey konum kaydedilmeli');
    assertSame($pageId, (int) $saved['page_id'], 'Sayfa bağlantısı kaydedilmeli');

    $figure = arc_extract_coast(arc_home()->body());
    assertContains('<circle cx="312" cy="104"', $figure, 'Nokta haritada doğru konumda olmalı');
    assertContains('/havran-web-tasarim', $figure, 'Nokta sayfaya bağlanmalı');

    $db->run('DELETE FROM pages');
    arc_logout_test();
});

test('F-16c', 'Sınır dışı konum değerleri kırpılır', function (): void {
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

    // Dogrulama sinir disi degeri reddeder; kayit olusmaz.
    $saved = $db->first('SELECT * FROM districts WHERE name = :name', [':name' => 'Sınır']);
    assertSame(null, $saved, 'Sınır dışı değer kabul edilmemeli');

    arc_logout_test();
});

test('F-P6-a', 'Bölüm listesi tüm bölümleri sabit sırada gösterir', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    $response = (new AdminHomeController())->index(Request::make('GET', admin_url('anasayfa')), []);
    assertSame(200, $response->status(), 'Liste açılmalı');

    $body = $response->body();
    foreach (HomeSection::LABELS as $key => $label) {
        assertContains('<code>' . $key . '</code>', $body, "Bölüm listede olmalı: {$key}");
    }

    // Sira sartnamedeki gibi
    $positions = [];
    foreach (array_keys(HomeSection::LABELS) as $key) {
        $positions[] = (int) strpos($body, '<code>' . $key . '</code>');
    }
    $sorted = $positions;
    sort($sorted);
    assertSame($sorted, $positions, 'Bölümler sabit sırada listelenmeli');

    arc_logout_test();
});

test('F-P6-b', 'Editör rolü anasayfa bölümlerini düzenleyebilir', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'editor');

    $response = (new AdminHomeController())->index(Request::make('GET', admin_url('anasayfa')), []);
    assertSame(200, $response->status(), 'Editör anasayfa yöneticisini görmeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});
