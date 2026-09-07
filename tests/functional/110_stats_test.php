<?php
/**
 * Istatistik, yedekleme ve zamanlanmis gorevler.
 * DOCS.md 8.4, 9.10, 9.11 — testler U-15, F-19
 */

declare(strict_types=1);

use Arcates\Core\Backup;
use Arcates\Core\Database;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Core\Visits;

/** Test icin ziyaret kaydi ekler. */
function arc_visit_row(Database $db, string $path, string $device = 'desktop', string $when = 'now', string $session = 'a'): void
{
    $db->insert('visits', [
        'path'         => $path,
        'session_hash' => str_pad($session, 64, '0'),
        'referrer'     => 'https://www.google.com/search?q=edremit',
        'device'       => $device,
        'lang'         => 'tr',
        'created_at'   => date('Y-m-d H:i:s', strtotime($when)),
    ]);
}

test('U-15e', 'Bot istekleri kaydedilir ama grafiğe girmez', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM visits');
    $db->run('DELETE FROM visits_daily');

    arc_visit_row($db, '/edremit-web-tasarim', 'desktop', 'now', 'ziyaretci1');
    arc_visit_row($db, '/edremit-web-tasarim', 'mobile', 'now', 'ziyaretci2');
    arc_visit_row($db, '/edremit-web-tasarim', 'bot', 'now', 'googlebot');
    arc_visit_row($db, '/edremit-web-tasarim', 'bot', 'now', 'bingbot');

    assertSame(4, (int) $db->count('visits'), 'Dört kayıt olmalı');

    $series = Visits::series(7);
    $today  = null;
    foreach ($series as $day) {
        if ($day['day'] === date('Y-m-d')) {
            $today = $day;
        }
    }

    assertTrue($today !== null, 'Bugün serisi bulunmalı');
    assertSame(2, $today['views'], 'Yalnızca bot olmayan istekler sayılmalı');
    assertSame(2, $today['sessions'], 'İki farklı oturum sayılmalı');
    assertSame(2, Visits::botCount(7), 'Bot sayısı ayrı raporlanmalı');

    $top = Visits::topPaths(7);
    assertSame('/edremit-web-tasarim', $top[0]['path']);
    assertSame(2, (int) $top[0]['views'], 'Botlar sayfa listesine de girmemeli');

    $devices = Visits::breakdown('device', 7);
    $labels  = array_column($devices, 'label');
    assertFalse(in_array('bot', $labels, true), 'Cihaz dağılımında bot olmamalı');

    $db->run('DELETE FROM visits');
});

test('F-P11-a', 'Panel ve varlık istekleri ziyaret olarak sayılmaz', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM visits');
    arc_logout_test();

    foreach (['/panel', '/panel/sayfalar', '/install', '/assets/css/site.css', '/uploads/2026/01/a.webp', '/sitemap.xml', '/robots.txt'] as $path) {
        Visits::record(
            Request::make('GET', $path, [], [], ['REMOTE_ADDR' => '203.0.113.5', 'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120']),
            'tr'
        );
    }

    assertSame(0, (int) $db->count('visits'), 'Bu yollar kaydedilmemeli');

    Visits::record(
        Request::make('GET', '/edremit-web-tasarim', [], [], ['REMOTE_ADDR' => '203.0.113.5', 'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120']),
        'tr'
    );

    assertSame(1, (int) $db->count('visits'), 'On yüz sayfası kaydedilmeli');

    $db->run('DELETE FROM visits');
});

test('F-P11-b', 'Panel oturumu açıkken ziyaret sayılmaz', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM visits');

    arc_login_as($db, 'admin');

    Visits::record(
        Request::make('GET', '/hakkimizda', [], [], ['REMOTE_ADDR' => '203.0.113.6', 'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120']),
        'tr'
    );

    assertSame(0, (int) $db->count('visits'), 'Yönetici kendi ziyaretini saymamalı');

    arc_logout_test();

    Visits::record(
        Request::make('GET', '/hakkimizda', [], [], ['REMOTE_ADDR' => '203.0.113.6', 'HTTP_USER_AGENT' => 'Mozilla/5.0 Chrome/120']),
        'tr'
    );

    assertSame(1, (int) $db->count('visits'), 'Ziyaretçi kaydı tutulmalı');

    $db->run('DELETE FROM visits');
});

test('F-P11-c', 'Eski ziyaretler günlük tabloya toplanır ve silinir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM visits');
    $db->run('DELETE FROM visits_daily');

    // 100 gun onceki kayitlar
    arc_visit_row($db, '/eski-sayfa', 'desktop', '-100 days', 'eski1');
    arc_visit_row($db, '/eski-sayfa', 'mobile', '-100 days', 'eski2');
    arc_visit_row($db, '/eski-sayfa', 'bot', '-100 days', 'eskibot');
    // Yeni kayit
    arc_visit_row($db, '/yeni-sayfa', 'desktop', 'now', 'yeni1');

    $result = Visits::rollup(90);

    assertSame(1, $result['rolled'], 'Bir gün/adres satırı toplanmalı');
    assertSame(3, $result['deleted'], 'Üç eski kayıt silinmeli (botlar dahil)');

    $daily = $db->first('SELECT * FROM visits_daily WHERE path = :path', [':path' => '/eski-sayfa']);
    assertTrue($daily !== null, 'Günlük kayıt oluşmalı');
    assertSame(2, (int) $daily['views'], 'Botlar toplamaya girmemeli');
    assertSame(2, (int) $daily['sessions']);

    assertSame(1, (int) $db->count('visits'), 'Yeni kayıt kalmalı');
    assertSame('/yeni-sayfa', (string) $db->value('SELECT path FROM visits LIMIT 1'));

    $db->run('DELETE FROM visits');
    $db->run('DELETE FROM visits_daily');
});

test('F-P11-d', 'Aylık CSV rapor üretilir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM visits');

    arc_visit_row($db, '/rapor-sayfasi', 'mobile', 'now', 'rapor1');

    $csv = Visits::monthlyCsv(date('Y-m'));

    assertContains('Gün', $csv, 'Başlık satırı bulunmalı');
    assertContains('/rapor-sayfasi', $csv, 'Adres raporda olmalı');
    assertContains('mobile', $csv, 'Cihaz raporda olmalı');

    $db->run('DELETE FROM visits');
});

test('F-19', 'Yedek alınır ve geri yüklendiğinde veri kaybı olmaz', function (): void {
    $db = arc_need_db();

    if (!function_exists('gzopen')) {
        skip('zlib eklentisi yok.');
    }

    // Bilinen bir durum olustur.
    $db->run('DELETE FROM projects');
    $marker = 'Yedek Testi ' . bin2hex(random_bytes(4));

    $projectId = $db->insert('projects', [
        'client_name' => $marker,
        'sector'      => 'Test',
        'district'    => 'Edremit',
        'status'      => 'published',
    ]);

    $before = (int) $db->count('projects');

    // Yedek al.
    $filename = Backup::create();
    assertTrue(Backup::path($filename) !== null, 'Yedek dosyası oluşmalı');
    assertContains('.sql.gz', $filename, 'Sıkıştırılmış SQL olmalı');

    // Veriyi boz: kaydi sil ve yeni bir kayit ekle.
    $db->run('DELETE FROM projects WHERE id = :id', [':id' => $projectId]);
    $db->insert('projects', ['client_name' => 'Yedekten sonra eklendi', 'status' => 'draft']);

    assertSame(null, $db->first('SELECT id FROM projects WHERE client_name = :n', [':n' => $marker]), 'Kayıt silinmiş olmalı');

    // Geri yukle.
    $statements = Backup::restore($filename);
    assertGreaterThan(0, $statements, 'İfadeler çalıştırılmalı');

    // Veri geri gelmeli, sonradan eklenen kayit gitmis olmali.
    $restored = $db->first('SELECT * FROM projects WHERE client_name = :n', [':n' => $marker]);
    assertTrue($restored !== null, 'Silinen kayıt geri gelmeli');
    assertSame('Edremit', $restored['district'], 'Alanlar bozulmadan geri gelmeli');
    assertSame($before, (int) $db->count('projects'), 'Kayıt sayısı yedek anındaki gibi olmalı');

    assertSame(
        null,
        $db->first('SELECT id FROM projects WHERE client_name = :n', [':n' => 'Yedekten sonra eklendi']),
        'Yedekten sonraki kayıt geri yüklemede gitmeli'
    );

    // Turkce karakterler ve tablo yapisi korunmali.
    assertTrue($db->tableExists('page_translations'), 'Tüm tablolar geri gelmeli');
    assertTrue($db->tableExists('home_sections'));

    Backup::delete($filename);
    $db->run('DELETE FROM projects');
});

test('F-19b', 'Yedek listesi son 10 kayıtla sınırlanır ve yol dışına çıkılamaz', function (): void {
    arc_need_db();

    assertSame(10, Backup::KEEP, 'Şartnamedeki sınır 10 olmalı');

    // Klasor disina cikma denemeleri reddedilir.
    assertSame(null, Backup::path('../../config/config.php'), 'Üst klasöre çıkılamamalı');
    assertSame(null, Backup::path('rastgele.sql.gz'), 'Beklenmeyen ad reddedilmeli');
    assertSame(null, Backup::path('arcates-2026-01-01-000000.sql.gz'), 'Var olmayan dosya null döndürmeli');
});

test('F-P11-e', 'Panel istatistik ve yedekleme ekranları açılır', function (): void {
    $db = arc_need_db();
    arc_login_as($db, 'admin');
    $db->run('DELETE FROM visits');

    arc_visit_row($db, '/istatistik-testi', 'desktop', 'now', 'istatistik');

    $stats = (new Arcates\Controllers\Admin\StatsController())->index(
        Request::make('GET', admin_url('istatistik')),
        []
    );
    assertSame(200, $stats->status(), 'İstatistik ekranı açılmalı');
    assertContains('/istatistik-testi', $stats->body(), 'Sayfa listede olmalı');
    assertContains('Cihaz dağılımı', $stats->body(), 'Cihaz dağılımı bulunmalı');
    assertContains('Dil dağılımı', $stats->body(), 'Dil dağılımı bulunmalı');

    $backups = (new Arcates\Controllers\Admin\BackupController())->index(
        Request::make('GET', admin_url('yedekleme')),
        []
    );
    assertSame(200, $backups->status(), 'Yedekleme ekranı açılmalı');
    assertContains('Şimdi yedek al', $backups->body(), 'Elle yedek alma bulunmalı');

    $db->run('DELETE FROM visits');
    arc_logout_test();
});

test('S-P11-a', 'Editör rolü yedeklemeye erişemez', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'editor');

    $response = (new Arcates\Controllers\Admin\BackupController())->index(
        Request::make('GET', admin_url('yedekleme')),
        []
    );
    assertSame(403, $response->status(), 'Editör için 403 dönmeli');

    // Istatistik editore aciktir.
    $stats = (new Arcates\Controllers\Admin\StatsController())->index(
        Request::make('GET', admin_url('istatistik')),
        []
    );
    assertSame(200, $stats->status(), 'Editör istatistiği görebilmeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});
