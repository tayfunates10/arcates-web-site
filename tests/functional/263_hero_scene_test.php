<?php
/**
 * Kahraman sahnesi — laptop ustu yuzen katmanlar.
 *
 * Sahne dekoratiftir: `aria-hidden` tasir, metni panelden gelir ve
 * bosaltilinca hic basilmaz. Uydurma proje sayaci ya da memnuniyet orani
 * tasimaz.  DOCS.md 5.1, 7.1
 */

declare(strict_types=1);

use Arcates\Controllers\Admin\HomeController as AdminHomeController;
use Arcates\Core\Migrator;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Core\Seeder;
use Arcates\Models\HomeSection;

/** Sahne gocunun ham SQL metni. */
const ARC_SCENE_MIGRATION = '2026_09_11_0001_kahraman_sahne_icerigi.sql';

test('F-HS-a', 'Tohumdaki sahne kart, rozet ve ray taşır', function (): void {
    $hero  = Seeder::sectionContent('hero', 'tr') ?? [];
    $scene = (array) ($hero['scene'] ?? []);

    assertTrue($scene !== [], 'Kahraman tohumunda sahne bulunmalı');
    assertTrue(($scene['card']['title'] ?? '') !== '', 'Sahne kartının başlığı olmalı');
    assertCount(2, (array) ($scene['chips'] ?? []), 'İki rozet tohumlanmalı');
    assertCount(5, (array) ($scene['rail'] ?? []), 'Yetkinlik rayında beş madde olmalı');

    // Ray maddeleri gercek hizmet listesinden gelmeli; sunulmayan hizmet yazilmaz.
    $cards = array_column((array) (Seeder::sectionContent('services', 'tr')['cards'] ?? []), 'title');
    foreach ((array) $scene['rail'] as $item) {
        assertTrue(
            in_array((string) ($item['label'] ?? ''), $cards, true),
            'Ray maddesi yayındaki hizmetlerden biri olmalı: ' . (string) ($item['label'] ?? '')
        );
    }
});

test('F-HS-b', 'Sahne uydurma ölçüm iddiası taşımaz', function (): void {
    $scene = (array) ((Seeder::sectionContent('hero', 'tr') ?? [])['scene'] ?? []);
    $text  = json_encode($scene, JSON_UNESCAPED_UNICODE) ?: '';

    // Referans kurguda gecen ama isletmenin arkasinda duramayacagi iddialar.
    foreach (['Tamamlanan Proje', 'Müşteri Memnuniyeti', '7/24', '%98', '248'] as $claim) {
        assertNotContains($claim, $text, 'Sahne doğrulanamayan iddia taşımamalı: ' . $claim);
    }
});

test('F-HS-c', 'Sahne anasayfada basılır ve dekoratif işaretlidir', function (): void {
    arc_need_db();
    HomeSection::ensureDefaults();

    $body = arc_home()->body();

    assertContains('ref-scene-card', $body, 'Sahne kartı basılmalı');
    assertContains('ref-scene-chip', $body, 'Rozetler basılmalı');
    assertContains('ref-scene-rail', $body, 'Yetkinlik rayı basılmalı');
    assertContains('css/reference-hero-scene.css', $body, 'Sahne stil katmanı yüklenmeli');

    // Sahne `ref-hero__visual` icinde; o kapsayici aria-hidden tasir.
    assertContains('class="ref-hero__visual" aria-hidden="true"', $body, 'Sahne dekoratif işaretli olmalı');
});

test('F-HS-d', 'Panelden boşaltılınca sahne kaybolur', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();

    $original = HomeSection::content('hero', 'tr');
    $stripped = $original;
    unset($stripped['scene']);
    HomeSection::saveContent('hero', 'tr', $stripped);

    $body = arc_home()->body();
    assertNotContains('ref-scene-card', $body, 'Sahne boşken kart basılmamalı');
    assertNotContains('ref-scene-rail', $body, 'Sahne boşken ray basılmamalı');

    HomeSection::saveContent('hero', 'tr', $original);
});

test('F-HS-e', 'Göç sahneyi yalnızca eksikse ekler', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();

    $sql = (string) file_get_contents(ARC_ROOT . '/db/migrations/' . ARC_SCENE_MIGRATION);
    assertContains('JSON_MERGE_PATCH', $sql, 'Göç JSON birleştirmesi kullanmalı');
    assertContains("JSON_EXTRACT(content, '\$.scene') IS NULL", $sql, 'Göç yalnızca eksik sahneye dokunmalı');

    $original = HomeSection::content('hero', 'tr');

    // 1) Sahnesi olmayan eski kurulum: goc sahneyi yaziyor.
    $stripped = $original;
    unset($stripped['scene']);
    HomeSection::saveContent('hero', 'tr', $stripped);
    (new Migrator($db))->runSqlScript($sql);
    assertTrue(HomeSection::content('hero', 'tr')['scene'] !== null, 'Eksik sahne göçle dolmalı');

    // 2) Elle girilmis sahne: goc dokunmuyor.
    $custom = $original;
    $custom['scene'] = ['card' => ['title' => 'İşletmenin kendi metni', 'text' => '']];
    HomeSection::saveContent('hero', 'tr', $custom);
    (new Migrator($db))->runSqlScript($sql);
    assertSame(
        'İşletmenin kendi metni',
        (string) (HomeSection::content('hero', 'tr')['scene']['card']['title'] ?? ''),
        'Elle girilmiş sahne korunmalı'
    );

    HomeSection::saveContent('hero', 'tr', $original);
});

test('F-HS-f', 'Panel kaydı sahneyi silmez', function (): void {
    $db = arc_need_db();
    HomeSection::ensureDefaults();
    arc_login_as($db, 'admin');

    $original = HomeSection::content('hero', 'tr');
    assertTrue(($original['scene']['card']['title'] ?? '') !== '', 'Başlangıçta sahne dolu olmalı');

    // Panel formu henuz sahne alani basmiyor; baslik duzenlemesi sahneyi
    // silmemeli. Bu kayip sessiz olurdu, bu yuzden ayrica sinaniyor.
    $post = [
        '_token'    => Security::csrfToken(),
        'is_active' => '1',
        'c'         => ['tr' => [
            'badge'       => 'Yeni rozet',
            'line1'       => 'Birinci satır',
            'line2'       => 'İkinci satır',
            'line3'       => 'Üçüncü satır',
            'description' => 'Yeni açıklama.',
            'cta1'        => ['label' => 'Teklif alın', 'url' => '/iletisim'],
            'cta2'        => ['label' => 'Örnekler', 'url' => '/referanslar'],
        ]],
    ];

    $response = (new AdminHomeController())->update(
        Request::make('POST', admin_url('anasayfa/hero'), $post),
        ['key' => 'hero']
    );
    assertSame(302, $response->status(), 'Kayıt sonrası yönlendirme dönmeli');

    $after = HomeSection::content('hero', 'tr');
    assertSame('Yeni rozet', (string) ($after['badge'] ?? ''), 'Gönderilen alan kaydedilmeli');
    assertSame(
        (string) ($original['scene']['card']['title'] ?? ''),
        (string) ($after['scene']['card']['title'] ?? ''),
        'Form sahne göndermediğinde saklanan sahne korunmalı'
    );
    assertCount(5, (array) ($after['scene']['rail'] ?? []), 'Ray maddeleri kayıptan sonra da durmalı');

    HomeSection::saveContent('hero', 'tr', $original);
    arc_logout_test();
});
