<?php
/**
 * Ornek site sunumu.
 *
 * `project` kayitlari, gercek musteri isleri yayina girene kadar "Ornek
 * Siteler" olarak sunulur. Adres bolum 4'teki gibi `/referanslar` kalir;
 * degisen yalnizca etiketler ve panelden bosaltilabilen uyari notudur.
 * DOCS.md 4.1, 9.4, 17
 */

declare(strict_types=1);

use Arcates\Controllers\Front\ProjectController;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Request;
use Arcates\Core\Settings;

/** Ornek site kaydi olusturur. */
function arc_sample_site(Database $db): int
{
    return \Arcates\Models\Project::save(
        [
            'client_name' => 'Örnek Körfez Otel',
            'sector'      => 'Konaklama',
            'district'    => 'Akçay',
            'status'      => 'published',
            'sort'        => 0,
        ],
        ['tr' => [
            'title'   => 'Akçay pansiyon rezervasyon sitesi',
            'slug'    => 'akcay-ornek',
            'excerpt' => 'Komisyonsuz doğrudan rezervasyon.',
            'content' => '<h2>Çözdüğü ihtiyaç</h2><p>Komisyon yükü.</p>',
            'robots'  => 'index,follow',
        ]]
    );
}

test('F-P14-a', 'Uyarı notu liste ve detayda görünür', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM projects');
    Lang::use('tr');
    arc_sample_site($db);

    Settings::set('projects_notice', 'Bunlar örnek kurgulardır.');

    $list = (new ProjectController())->index(Request::make('GET', '/referanslar'), []);
    assertSame(200, $list->status(), 'Liste açılmalı');
    assertContains('Bunlar örnek kurgulardır.', $list->body(), 'Not listede görünmeli');

    $detail = (new ProjectController())->show(Request::make('GET', '/referanslar/akcay-ornek'), ['slug' => 'akcay-ornek']);
    assertSame(200, $detail->status(), 'Detay açılmalı');
    assertContains('Bunlar örnek kurgulardır.', $detail->body(), 'Not detayda görünmeli');
});

test('F-P14-b', 'Ayar boşaltılınca not kaybolur', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM projects');
    Lang::use('tr');
    arc_sample_site($db);

    Settings::set('projects_notice', '');

    $list = (new ProjectController())->index(Request::make('GET', '/referanslar'), []);
    assertNotContains('class="notice"', $list->body(), 'Ayar boşken not basılmamalı');

    $detail = (new ProjectController())->show(Request::make('GET', '/referanslar/akcay-ornek'), ['slug' => 'akcay-ornek']);
    assertNotContains('class="notice"', $detail->body(), 'Ayar boşken detayda da not olmamalı');

    Settings::set('projects_notice', 'Bunlar örnek kurgulardır.');
});

test('F-P14-c', 'Etiket "Örnek Siteler", adres /referanslar olarak kalır', function (): void {
    $tr = require ARC_ROOT . '/lang/tr.php';
    assertSame('Örnek Siteler', $tr['projects'], 'Etiket örnek site olmalı');

    $pages = array_column(require ARC_ROOT . '/db/seed/pages.php', null, 'slug');
    assertTrue(isset($pages['referanslar']), 'Adres bölüm 4\'teki gibi /referanslar kalmalı');
    assertSame('Örnek Siteler', $pages['referanslar']['title'], 'Sayfa başlığı örnek site olmalı');
});

test('F-P14-d', 'Tohum kayıtları teslim edilmiş iş iddiası taşımaz', function (): void {
    $projects = require ARC_ROOT . '/db/seed/projects.php';
    assertTrue($projects !== [], 'Tohumda örnek site kaydı olmalı');

    foreach ($projects as $row) {
        assertTrue(
            str_starts_with((string) $row['client'], 'Örnek '),
            "Müşteri adı 'Örnek ' ile başlamalı: {$row['client']}"
        );
        assertNotContains('Yapılanlar', (string) $row['content'], "{$row['slug']}: yapılmış iş dili kalmamalı");
    }

    $notice = (string) (\Arcates\Core\Settings::defaults()['projects_notice'] ?? '');
    assertContains('örnek', mb_strtolower($notice, 'UTF-8'), 'Varsayılan not örnek olduğunu söylemeli');
    assertContains('değildir', $notice, 'Varsayılan not müşteri işi olmadığını söylemeli');
});


test('F-P14-e', 'Not ayari bulunmayan eski kurulumda varsayilan aciklama gorunur', function (): void {
    $db = arc_need_db();
    Lang::use('tr');
    $notice = Settings::get('projects_notice', '');
    try {
        $db->delete('settings', ['key' => 'projects_notice']);
        Settings::flush();
        $list = (new ProjectController())->index(Request::make('GET', '/referanslar'), []);
        assertContains(Settings::defaults()['projects_notice'], $list->body());
    } finally {
        Settings::set('projects_notice', $notice);
    }
});
