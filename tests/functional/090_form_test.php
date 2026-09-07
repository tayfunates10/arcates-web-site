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
        'name'     => 'Ayşe Yılmaz',
        'phone'    => '0532 111 22 33',
        'email'    => 'ayse@ornek.test',
        'service'  => 'Web tasarım',
        'message'  => 'Zeytinyağı işletmemiz için bir site istiyoruz.',
        'kvkk'     => '1',
    ], $overrides);
}

/** Iletisim sayfasini olusturur. */
function arc_contact_page(Database $db): void
{
    $db->run('DELETE FROM pages');
    arc_page($db, [], [
        'title'   => 'İletişim',
        'slug'    => 'iletisim',
        'content' => '<p>Bize yazın.</p>',
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

test('F-08', 'Form gönderilince kayıt oluşur, e-posta gider, source_url doğru', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);

    $response = arc_submit(arc_form_post());

    assertSame(302, $response->status(), 'Teşekkür sayfasına yönlendirmeli');
    assertContains('/tesekkurler', (string) $response->headerLine('Location'), 'Ayrı URL kullanılmalı');

    $row = $db->first('SELECT * FROM submissions ORDER BY id DESC LIMIT 1');
    assertTrue($row !== null, 'Kayıt oluşmalı');
    assertSame('Ayşe Yılmaz', $row['name']);
    assertSame('ayse@ornek.test', $row['email'], 'E-posta küçük harfe çevrilmeli');
    assertSame('/edremit-web-tasarim', $row['source_url'], 'Kaynak sayfa kaydedilmeli');
    assertSame('tr', $row['lang'], 'Dil kaydedilmeli');
    assertSame(1, (int) $row['kvkk_consent'], 'KVKK onayı kaydedilmeli');
    assertSame('new', $row['status'], 'Başlangıç durumu yeni olmalı');
    assertTrue($row['ip'] !== null, 'IP kaydedilmeli');

    $sent = Mailer::sentMessages();
    assertCount(1, $sent, 'Yöneticiye bir e-posta gitmeli');
    assertContains('Ayşe Yılmaz', $sent[0]['subject'], 'Konu göndereni içermeli');
    assertContains('/edremit-web-tasarim', $sent[0]['body'], 'Kaynak sayfa e-postada olmalı');
    assertSame('ayse@ornek.test', $sent[0]['headers']['Reply-To'], 'Yanıt adresi gönderen olmalı');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('F-08b', 'UTM parametreleri kayda geçer', function (): void {
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
    assertTrue($row !== null, 'Kayıt oluşmalı');
    assertSame('google', $row['utm']['utm_source'] ?? null, 'UTM kaynak kaydedilmeli');
    assertSame('cpc', $row['utm']['utm_medium'] ?? null);
    assertSame('edremit-2026', $row['utm']['utm_campaign'] ?? null);
    assertContains('google.com', (string) $row['referrer'], 'Geldiği yer kaydedilmeli');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('S-14', 'Formu 1 saniyede göndermek reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);

    $response = arc_submit(arc_form_post([], 1), '198.51.100.22');

    assertSame(302, $response->status(), 'Yönlendirme dönmeli');
    assertNotContains('/tesekkurler', (string) $response->headerLine('Location'), 'Teşekkür sayfasına gitmemeli');
    assertSame(0, (int) $db->count('submissions'), 'Kayıt oluşmamalı');
    assertCount(0, Mailer::sentMessages(), 'E-posta gitmemeli');

    // 3 saniye ve uzeri kabul edilir.
    $ok = arc_submit(arc_form_post([], 4), '198.51.100.23');
    assertContains('/tesekkurler', (string) $ok->headerLine('Location'), '4 saniye sonra gönderim kabul edilmeli');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('S-15', 'Honeypot dolu gönderim sessizce reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);

    $response = arc_submit(arc_form_post([ContactController::HONEYPOT => 'https://spam.example']), '198.51.100.24');

    // Bot basarili sanmali; ama kayit olusmamali.
    assertSame(302, $response->status());
    assertContains('/tesekkurler', (string) $response->headerLine('Location'), 'Bot başarılı sanmalı');
    assertSame(0, (int) $db->count('submissions'), 'Kayıt oluşmamalı');
    assertCount(0, Mailer::sentMessages(), 'E-posta gitmemeli');

    Mailer::capture(false);
});

test('S-16', 'Aynı IP\'den saatte 6. gönderim reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    Mailer::capture(true);
    $ip = '198.51.100.25';

    for ($i = 1; $i <= 5; $i++) {
        $response = arc_submit(arc_form_post(['email' => "gonderim{$i}@ornek.test"]), $ip);
        assertContains('/tesekkurler', (string) $response->headerLine('Location'), "Gönderim {$i} kabul edilmeli");
    }

    assertSame(5, (int) $db->count('submissions'), 'Beş kayıt oluşmalı');

    $sixth = arc_submit(arc_form_post(['email' => 'altinci@ornek.test']), $ip);
    assertNotContains('/tesekkurler', (string) $sixth->headerLine('Location'), '6. gönderim reddedilmeli');
    assertSame(5, (int) $db->count('submissions'), 'Yeni kayıt oluşmamalı');

    // Baska IP etkilenmemeli.
    $other = arc_submit(arc_form_post(['email' => 'baska@ornek.test']), '198.51.100.26');
    assertContains('/tesekkurler', (string) $other->headerLine('Location'), 'Başka IP etkilenmemeli');

    Mailer::capture(false);
    $db->run('DELETE FROM submissions');
});

test('S-P9-a', 'CSRF belirteci olmayan form gönderimi 419 döner', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    arc_reset_session();
    Security::csrfToken();

    $post = arc_form_post();
    $post['_token'] = 'yanlis-belirtec';

    $response = arc_submit($post, '198.51.100.27');
    assertSame(419, $response->status(), 'Geçersiz belirteç 419 döndürmeli');
});

test('S-P9-b', 'Eksik veya geçersiz alanlar sunucu tarafında reddedilir', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    $db->run('DELETE FROM submissions');
    arc_reset_session();

    // KVKK onayi olmadan
    $noConsent = arc_submit(arc_form_post(['kvkk' => '']), '198.51.100.28');
    assertNotContains('/tesekkurler', (string) $noConsent->headerLine('Location'), 'Onaysız gönderim reddedilmeli');
    assertSame(0, (int) $db->count('submissions'));

    // Gecersiz e-posta
    arc_reset_session();
    $badEmail = arc_submit(arc_form_post(['email' => 'gecersiz']), '198.51.100.29');
    assertNotContains('/tesekkurler', (string) $badEmail->headerLine('Location'), 'Geçersiz e-posta reddedilmeli');

    // Cok kisa mesaj
    arc_reset_session();
    $shortMessage = arc_submit(arc_form_post(['message' => 'kisa']), '198.51.100.30');
    assertNotContains('/tesekkurler', (string) $shortMessage->headerLine('Location'), 'Çok kısa mesaj reddedilmeli');

    assertSame(0, (int) $db->count('submissions'), 'Hiçbir kayıt oluşmamalı');
});

test('F-09', 'Durum kazanıldı yapılınca dönüşüm raporuna yansır', function (): void {
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
            'note'   => 'Sözleşme imzalandı.',
        ]),
        ['id' => $id]
    );

    assertSame(302, $response->status());

    $row = Submission::get($id);
    assertSame('won', $row['status'], 'Durum kaydedilmeli');
    assertSame('Sözleşme imzalandı.', $row['note'], 'Not kaydedilmeli');

    $report = Submission::conversionReport();
    assertGreaterThan(0, count($report), 'Rapor satırı olmalı');

    $found = null;
    foreach ($report as $line) {
        if ($line['source_url'] === '/edremit-web-tasarim') {
            $found = $line;
        }
    }

    assertTrue($found !== null, 'Kaynak sayfa raporda olmalı');
    assertSame(1, (int) $found['won'], 'Kazanım sayılmalı');
    assertSame(1, (int) $found['total'], 'Toplam kayıt sayılmalı');

    arc_logout_test();
    $db->run('DELETE FROM submissions');
});

test('F-P9-a', 'Saklama süresi dolan kayıtlar silinir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM submissions');

    // Biri eski, biri yeni.
    $db->insert('submissions', [
        'form_key' => 'contact', 'name' => 'Eski kayıt', 'email' => 'eski@ornek.test',
        'status' => 'lost', 'created_at' => date('Y-m-d H:i:s', strtotime('-800 days')),
    ]);
    $db->insert('submissions', [
        'form_key' => 'contact', 'name' => 'Yeni kayıt', 'email' => 'yeni@ornek.test',
        'status' => 'new', 'created_at' => date('Y-m-d H:i:s'),
    ]);

    $deleted = Submission::purgeExpired(730);

    assertSame(1, $deleted, 'Bir kayıt silinmeli');
    assertSame(1, (int) $db->count('submissions'), 'Yeni kayıt kalmalı');
    assertSame('Yeni kayıt', (string) $db->value('SELECT name FROM submissions LIMIT 1'));

    $db->run('DELETE FROM submissions');
});

test('F-P9-b', 'CSV dışa aktarma tüm alanları içerir', function (): void {
    $db = arc_need_db();
    $db->run('DELETE FROM submissions');

    $db->insert('submissions', [
        'form_key' => 'contact', 'name' => 'Mehmet Kaya', 'email' => 'mehmet@ornek.test',
        'phone' => '0533 000 00 00', 'service' => 'E-ticaret',
        'message' => "İki satırlı\nmesaj", 'source_url' => '/e-ticaret-sitesi',
        'utm' => Security::json(['utm_source' => 'google']), 'lang' => 'tr',
        'kvkk_consent' => 1, 'status' => 'quoted',
    ]);

    $csv = Submission::toCsv(Submission::listing());

    assertContains('Mehmet Kaya', $csv, 'Ad CSV\'de olmalı');
    assertContains('mehmet@ornek.test', $csv);
    assertContains('/e-ticaret-sitesi', $csv, 'Kaynak sayfa CSV\'de olmalı');
    assertContains('google', $csv, 'UTM kaynak CSV\'de olmalı');
    assertContains('Teklif', $csv, 'Durum etiketi çevrilmiş olmalı');
    assertContains('İki satırlı mesaj', $csv, 'Satır sonları düzleştirilmeli');
    assertContains('Kaynak sayfa', $csv, 'Başlık satırı bulunmalı');

    $db->run('DELETE FROM submissions');
});

test('F-P9-c', 'Teşekkür sayfası ayrı adrestir ve noindex taşır', function (): void {
    $db = arc_need_db();
    Lang::use('tr');

    $response = (new ContactController())->thanks(Request::make('GET', '/tesekkurler'), []);

    assertSame(200, $response->status(), 'Sayfa açılmalı');

    $body = $response->body();
    assertContains('noindex', $body, 'Dizine girmemeli');
    assertContains('/tesekkurler', $body, 'Kendi adresini canonical olarak vermeli');
    assertSame(1, substr_count($body, '<h1'), 'Tek H1 bulunmalı');
});

test('E-06', 'Form alanlarının her biri label ile bağlıdır', function (): void {
    $db = arc_need_db();
    arc_contact_page($db);
    arc_reset_session();
    Lang::use('tr');

    $body = (new ContactController())->show(Request::make('GET', '/iletisim'), ['slug' => 'iletisim'])->body();

    // Formdaki her alanin bir label'i olmali.
    preg_match_all('/<(input|select|textarea)\b[^>]*\bid="([^"]+)"[^>]*>/', $body, $fields, PREG_SET_ORDER);
    assertGreaterThan(4, count($fields), 'Form alanları bulunmalı');

    foreach ($fields as $field) {
        if (in_array($field[1], ['input'], true) && preg_match('/type="hidden"/', $field[0]) === 1) {
            continue;
        }
        assertContains('for="' . $field[2] . '"', $body, "Alan label ile bağlanmalı: {$field[2]}");
    }

    // Honeypot gorunur akista degil ve klavye ile ulasilamaz.
    assertContains('form__trap', $body, 'Honeypot sarmalayıcısı bulunmalı');
    assertContains('tabindex="-1"', $body, 'Honeypot klavye ile ulaşılamaz olmalı');
    assertContains('aria-hidden="true"', $body, 'Honeypot ekran okuyuculardan gizli olmalı');

    // KVKK onay kutusu onceden isaretli degil. DOCS.md 10.9
    assertTrue(
        preg_match('/<input[^>]*id="form_kvkk"[^>]*>/', $body, $checkbox) === 1,
        'KVKK kutusu bulunmalı'
    );
    assertNotContains('checked', $checkbox[0], 'Onay kutusu önceden işaretli olmamalı');
});
