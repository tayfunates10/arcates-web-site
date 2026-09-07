<?php
/**
 * Teklif formu, spam korumasi ve donusum takibi.
 * DOCS.md 10.8, 10.9, 12 — testler F-08, F-09, S-14, S-15, S-16
 */

declare(strict_types=1);

use Arcates\Controllers\Front\ContactController;
use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Mailer;
use Arcates\Core\Request;
use Arcates\Core\Security;
use Arcates\Core\Session;
use Arcates\Models\Submission;

/** Gecerli bir form gonderimi uretir. */
function arc_form_post(array $overrides = [], int $openedSecondsAgo = 30): array
{
    return array_merge([
        '_token'   => Security::csrfToken(),
        '_opened'  => (string) (time() - $openedSecondsAgo),
        '_return'  => 'iletisim',
        '_source'  => '/edremit-web-tasarim',
        'name'     => 'Ayse Yilmaz',
        'phone'    => '0532 111 22 33',
        'email'    => 'ayse@ornek.test',
        'service'  => 'Web tasarim',
        'message'  => 'Zeytinyagi isletmemiz icin bir site istiyoruz.',
        'kvkk'     => '1',
    ], $overrides);
}

/** Iletisim sayfasini olusturur. */
function arc_contact_page(Database $db): void
{
    $db->run('DELETE FROM pages');
    arc_page($db, [], [
        'title'   => 'Iletisim',
        'slug'    => 'iletisim',
        'content' => '<p>Bize yazin.</p>',
    ]);
}

function arc_submit(array $post, string $ip = '198.51.100.20'): Arcates\Core\Response
{
    Lang::use('tr');
    return (new ContactController())->submit(
        Request::make('POST', '/iletisim', $post, [], ['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => 'ArcatesTest/1.0']),
        []
    );
}

test('F-08', 'Form gonderilince kayit olusur, e-posta gider, source_url dogru', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);

    $response = arc_submit(arc_form_post());

    assertSame(302, $response->status(), 'Tesekkur sayfasina yonlendirmeli');
    assertContains('/tesekkurler', (string) $response->headerLine('Location'), 'Ayri URL kullanilmali');

    $row = $db->first('SELECT * FROM submissions ORDER BY id DESC LIMIT 1');
    assertTrue($row !== null, 'Kayit olusmali');
    assertSame('Ayse Yilmaz', $row['name']);
    assertSame('ayse@ornek.test', $row['email'], 'E-posta kucuk harfe cevrilmeli');
    assertSame('/edremit-web-tasarim', $row['source_url'], 'Kaynak sayfa kaydedilmeli');
    assertSame('tr', $row['lang'], 'Dil kaydedilmeli');
    assertSame(1, (int) $row['kvkk_consent'], 'KVKK onayi kaydedilmeli');
    assertSame('new', $row['status'], 'Baslangic durumu yeni olmali');
    assertTrue($row['ip'] !== null, 'IP kaydedilmeli');

    $sent = Mailer::sentMessages();
    assertCount(1, $sent, 'Yoneticiye bir e-posta gitmeli');
    assertContains('Ayse Yilmaz', $sent[0]['subject'], 'Konu gondereni icermeli');
    assertContains('/edremit-web-tasarim', $sent[0]['body'], 'Kaynak sayfa e-postada olmali');
    assertSame('ayse@ornek.test', $sent[0]['headers']['Reply-To'], 'Yanit adresi gonderen olmali');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('F-08b', 'UTM parametreleri kayda gecer', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);
    Lang::use('tr');

    (new ContactController())->submit(
        Request::make(
            'POST',
            '/iletisim',
            arc_form_post(['email' => 'utm@ornek.test']),
            ['utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'edremit-2026'],
            ['REMOTE_ADDR' => '198.51.100.21', 'HTTP_USER_AGENT' => 'ArcatesTest/1.0', 'HTTP_REFERER' => 'https://www.google.com/']
        ),
        []
    );

    $row = Submission::get((int) $db->value('SELECT MAX(id) FROM submissions'));
    assertTrue($row !== null, 'Kayit olusmali');
    assertSame('google', $row['utm']['utm_source'] ?? null, 'UTM kaynak kaydedilmeli');
    assertSame('cpc', $row['utm']['utm_medium'] ?? null);
    assertSame('edremit-2026', $row['utm']['utm_campaign'] ?? null);
    assertContains('google.com', (string) $row['referrer'], 'Geldigi yer kaydedilmeli');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('S-14', 'Formu 1 saniyede gondermek reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);

    $response = arc_submit(arc_form_post([], 1), '198.51.100.22');

    assertSame(302, $response->status(), 'Yonlendirme donmeli');
    assertNotContains('/tesekkurler', (string) $response->headerLine('Location'), 'Tesekkur sayfasina gitmemeli');
    assertSame(0, (int) $db->count('submissions'), 'Kayit olusmamali');
    assertCount(0, Mailer::sentMessages(), 'E-posta gitmemeli');

    // 3 saniye ve uzeri kabul edilir.
    $ok = arc_submit(arc_form_post([], 4), '198.51.100.23');
    assertContains('/tesekkurler', (string) $ok->headerLine('Location'), '4 saniye sonra gonderim kabul edilmeli');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('S-15', 'Honeypot dolu gonderim sessizce reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);

    $response = arc_submit(arc_form_post([ContactController::HONEYPOT => 'https://spam.example']), '198.51.100.24');

    // Bot basarili sanmali; ama kayit olusmamali.
    assertSame(302, $response->status());
    assertContains('/tesekkurler', (string) $response->headerLine('Location'), 'Bot basarili sanmali');
    assertSame(0, (int) $db->count('submissions'), 'Kayit olusmamali');
    assertCount(0, Mailer::sentMessages(), 'E-posta gitmemeli');

    Mailer::capture(false);
});

test('S-16', 'Ayni IP\'den saatte 6. gonderim reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);
    $ip = '198.51.100.25';

    for ($i = 1; $i <= 5; $i++) {
        $response = arc_submit(arc_form_post(['email' => "gonderim{$i}@ornek.test"]), $ip);
        assertContains('/tesekkurler', (string) $response->headerLine('Location'), "Gonderim {$i} kabul edilmeli");
    }

    assertSame(5, (int) $db->count('submissions'), 'Bes kayit olusmali');

    $sixth = arc_submit(arc_form_post(['email' => 'altinci@ornek.test']), $ip);
    assertNotContains('/tesekkurler', (string) $sixth->headerLine('Location'), '6. gonderim reddedilmeli');
    assertSame(5, (int) $db->count('submissions'), 'Yeni kayit olusmamali');

    // Baska IP etkilenmemeli.
    $other = arc_submit(arc_form_post(['email' => 'baska@ornek.test']), '198.51.100.26');
    assertContains('/tesekkurler', (string) $other->headerLine('Location'), 'Baska IP etkilenmemeli');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('S-P9-a', 'CSRF belirteci olmayan form gonderimi 419 doner', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    arc_reset_session();
    Security::csrfToken();

    $post = arc_form_post();
    $post['_token'] = 'yanlis-belirtec';

    $response = arc_submit($post, '198.51.100.27');
    assertSame(419, $response->status(), 'Gecersiz belirtec 419 dondurmeli');
});

test('S-P9-b', 'Eksik veya gecersiz alanlar sunucu tarafinda reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    // KVKK onayi olmadan
    $noConsent = arc_submit(arc_form_post(['kvkk' => '']), '198.51.100.28');
    assertNotContains('/tesekkurler', (string) $noConsent->headerLine('Location'), 'Onaysiz gonderim reddedilmeli');
    assertSame(0, (int) $db->count('submissions'));

    // Gecersiz e-posta
    arc_reset_session();
    $badEmail = arc_submit(arc_form_post(['email' => 'gecersiz']), '198.51.100.29');
    assertNotContains('/tesekkurler', (string) $badEmail->headerLine('Location'), 'Gecersiz e-posta reddedilmeli');

    // Cok kisa mesaj
    arc_reset_session();
    $shortMessage = arc_submit(arc_form_post(['message' => 'kisa']), '198.51.100.30');
    assertNotContains('/tesekkurler', (string) $shortMessage->headerLine('Location'), 'Cok kisa mesaj reddedilmeli');

    assertSame(0, (int) $db->count('submissions'), 'Hicbir kayit olusmamali');
});

test('F-09', 'Durum kazanildi yapilinca donusum raporuna yansir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);
    arc_submit(arc_form_post(), '198.51.100.31');
    Mailer::capture(false);

    $id = (int) $db->value('SELECT MAX(id) FROM submissions');
    arc_login_as($db, 'admin');

    $response = (new Arcates\Controllers\Admin\SubmissionController())->update(
        Request::make('POST', admin_url('formlar/' . $id), [
            '_token' => Security::csrfToken(),
            'status' => 'won',
            'note'   => 'Sozlesme imzalandi.',
        ]),
        ['id' => $id]
    );

    assertSame(302, $response->status());

    $row = Submission::get($id);
    assertSame('won', $row['status'], 'Durum kaydedilmeli');
    assertSame('Sozlesme imzalandi.', $row['note'], 'Not kaydedilmeli');

    $report = Submission::conversionReport();
    assertGreaterThan(0, count($report), 'Rapor satiri olmali');

    $found = null;
    foreach ($report as $line) {
        if ($line['source_url'] === '/edremit-web-tasarim') {
            $found = $line;
        }
    }

    assertTrue($found !== null, 'Kaynak sayfa raporda olmali');
    assertSame(1, (int) $found['won'], 'Kazanim sayilmali');
    assertSame(1, (int) $found['total'], 'Toplam kayit sayilmali');

    arc_logout_test();
    $db->run('DELETE FROM submissions');
});

test('F-P9-a', 'Saklama suresi dolan kayitlar silinir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM submissions');

    // Biri eski, biri yeni.
    $db->insert('submissions', [
        'form_key' => 'contact', 'name' => 'Eski kayit', 'email' => 'eski@ornek.test',
        'status' => 'lost', 'created_at' => date('Y-m-d H:i:s', strtotime('-800 days')),
    ]);
    $db->insert('submissions', [
        'form_key' => 'contact', 'name' => 'Yeni kayit', 'email' => 'yeni@ornek.test',
        'status' => 'new', 'created_at' => date('Y-m-d H:i:s'),
    ]);

    $deleted = Submission::purgeExpired(730);

    assertSame(1, $deleted, 'Bir kayit silinmeli');
    assertSame(1, (int) $db->count('submissions'), 'Yeni kayit kalmali');
    assertSame('Yeni kayit', (string) $db->value('SELECT name FROM submissions LIMIT 1'));

    $db->run('DELETE FROM submissions');
});

test('F-P9-b', 'CSV disa aktarma tum alanlari icerir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM submissions');

    $db->insert('submissions', [
        'form_key' => 'contact', 'name' => 'Mehmet Kaya', 'email' => 'mehmet@ornek.test',
        'phone' => '0533 000 00 00', 'service' => 'E-ticaret',
        'message' => "Iki satirli\nmesaj", 'source_url' => '/e-ticaret-sitesi',
        'utm' => Security::json(['utm_source' => 'google']), 'lang' => 'tr',
        'kvkk_consent' => 1, 'status' => 'quoted',
    ]);

    $csv = Submission::toCsv(Submission::listing());

    assertContains('Mehmet Kaya', $csv, 'Ad CSV\'de olmali');
    assertContains('mehmet@ornek.test', $csv);
    assertContains('/e-ticaret-sitesi', $csv, 'Kaynak sayfa CSV\'de olmali');
    assertContains('google', $csv, 'UTM kaynak CSV\'de olmali');
    assertContains('Teklif', $csv, 'Durum etiketi cevrilmiş olmali');
    assertContains('Iki satirli mesaj', $csv, 'Satir sonlari duzlestirilmeli');
    assertContains('Kaynak sayfa', $csv, 'Baslik satiri bulunmali');

    $db->run('DELETE FROM submissions');
});

test('F-P9-c', 'Tesekkur sayfasi ayri adrestir ve noindex tasir', function (): void {
    $db = arc_need_db();
    Lang::use('tr');

    $response = (new ContactController())->thanks(Request::make('GET', '/tesekkurler'), []);

    assertSame(200, $response->status(), 'Sayfa acilmali');

    $body = $response->body();
    assertContains('noindex', $body, 'Dizine girmemeli');
    assertContains('/tesekkurler', $body, 'Kendi adresini canonical olarak vermeli');
    assertSame(1, substr_count($body, '<h1'), 'Tek H1 bulunmali');
});

test('E-06', 'Form alanlarinin her biri label ile baglidir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    arc_reset_session();
    Lang::use('tr');

    $body = (new ContactController())->show(Request::make('GET', '/iletisim'), ['slug' => 'iletisim'])->body();

    // Formdaki her alanin bir label'i olmali.
    preg_match_all('/<(input|select|textarea)\b[^>]*\bid="([^"]+)"[^>]*>/', $body, $fields, PREG_SET_ORDER);
    assertGreaterThan(4, count($fields), 'Form alanlari bulunmali');

    foreach ($fields as $field) {
        if (in_array($field[1], ['input'], true) && preg_match('/type="hidden"/', $field[0]) === 1) {
            continue;
        }
        assertContains('for="' . $field[2] . '"', $body, "Alan label ile baglanmali: {$field[2]}");
    }

    // Honeypot gorunur akista degil ve klavye ile ulasilamaz.
    assertContains('form__trap', $body, 'Honeypot sarmalayicisi bulunmali');
    assertContains('tabindex="-1"', $body, 'Honeypot klavye ile ulasilamaz olmali');
    assertContains('aria-hidden="true"', $body, 'Honeypot ekran okuyuculardan gizli olmali');

    // KVKK onay kutusu onceden isaretli degil. DOCS.md 10.9
    assertTrue(
        preg_match('/<input[^>]*id="form_kvkk"[^>]*>/', $body, $checkbox) === 1,
        'KVKK kutusu bulunmali'
    );
    assertNotContains('checked', $checkbox[0], 'Onay kutusu onceden isaretli olmamali');
});
