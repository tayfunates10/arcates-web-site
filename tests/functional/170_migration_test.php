<?php
/**
 * Ayar ve dil uyumlastirma gocu.
 *
 * `Seeder::settings()` yalnizca eksik satiri ekler, `Settings::get()` de
 * veritabani satirini okur. Bu yuzden `Settings::defaults()` icindeki bir
 * degeri degistirmek kurulmus bir siteyi etkilemez; goc o farki kapatir.
 * Elle girilmis degerler korunmali, yalnizca eski varsayilanin aynen durdugu
 * satir guncellenmelidir.  DOCS.md 8.6, 9.4, 11.3
 */

declare(strict_types=1);

use Arcates\Core\Database;
use Arcates\Core\Lang;
use Arcates\Core\Migrator;
use Arcates\Core\Settings;

/** Goc dosyasinin adi. Testler tek kaynaktan okur. */
const ARC_MIGRATION_0001 = '2026_09_08_0001_ayar_ve_dil_uyumlastirma.sql';

/** Goc dosyasinin ham SQL metni. */
function arc_migration_sql(): string
{
    return (string) file_get_contents(ARC_ROOT . '/db/migrations/' . ARC_MIGRATION_0001);
}

/** Gocu test veritabaninda calistirir ve ayar onbellegini bosaltir. */
function arc_run_migration(Database $db): int
{
    $count = (new Migrator($db))->runSqlScript(arc_migration_sql());
    Settings::flush();
    Lang::reset();
    return $count;
}

/** Bir ayarin ham satir degerini okur; satir yoksa null doner. */
function arc_setting_row(Database $db, string $key): ?string
{
    $value = $db->value('SELECT `value` FROM settings WHERE `key` = :key', [':key' => $key]);
    return $value === null ? null : (string) $value;
}

/** Testten sonra tohum durumunu geri yukler. */
function arc_restore_seed_state(Database $db): void
{
    foreach (['nap_street', 'nap_phone', 'nap_email', 'projects_notice'] as $key) {
        $db->delete('settings', ['key' => $key]);
        $db->insert('settings', [
            'key'      => $key,
            'value'    => (string) Settings::defaults()[$key],
            'autoload' => 1,
        ]);
    }

    arc_activate_langs(['tr']);
    Settings::flush();
}

test('F-P17-a', 'Göç dosyası beş koşullu ifadeye ayrılır', function (): void {
    $sql = arc_migration_sql();
    assertNotSame('', $sql, 'Göç dosyası boş olmamalı');

    $statements = Migrator::splitStatements($sql);
    assertSame(5, count($statements), 'Göç beş ifadeye ayrılmalı');

    foreach ($statements as $statement) {
        if (str_starts_with(strtoupper($statement), 'UPDATE')) {
            assertTrue(
                str_contains(strtoupper($statement), ' WHERE '),
                'Koşulsuz UPDATE elle girilmiş değeri ezer: ' . mb_substr($statement, 0, 60)
            );
        }
    }

    assertContains('INSERT IGNORE INTO settings', $sql, 'Eksik ayar satırı eklenmeli, var olan ezilmemeli');
});

test('F-P17-b', 'Eski kurulumdaki varsayılanlar yeni değere geçer', function (): void {
    $db = arc_need_db();

    // Eski kurulum: uzun adres, bos telefon, ilk fazin e-postasi, notu olmayan
    // ayar tablosu.
    $db->update('settings', ['value' => 'Tuzcumurat Mah. 27016 Sk. Uysal Apt. No: 5 Kat: 3 Daire: 8'], ['key' => 'nap_street']);
    $db->update('settings', ['value' => ''], ['key' => 'nap_phone']);
    $db->update('settings', ['value' => 'info@arcates.com'], ['key' => 'nap_email']);
    $db->delete('settings', ['key' => 'projects_notice']);
    Settings::flush();

    // Goc olmadan: kurulmus site eski degeri gostermeye devam eder.
    assertSame(
        'Tuzcumurat Mah. 27016 Sk. Uysal Apt. No: 5 Kat: 3 Daire: 8',
        (string) Settings::get('nap_street', ''),
        'Göç uygulanmadan önce eski adres duruyor olmalı'
    );
    assertSame('', (string) Settings::get('projects_notice', ''), 'Satır yokken not boş dönmeli');

    arc_run_migration($db);

    assertSame('Tuzcumurat Mah. 27016 Sk. No: 5', (string) Settings::get('nap_street', ''), 'Adres kısalmalı');
    assertSame('+90 545 946 50 73', (string) Settings::get('nap_phone', ''), 'Boş telefon dolmalı');
    assertSame('info@arcatesyazilim.com', (string) Settings::get('nap_email', ''), 'Yer tutucu e-posta değişmeli');
    assertContains('örnek kurgulardır', (string) Settings::get('projects_notice', ''), 'Örnek site notu eklenmeli');

    arc_restore_seed_state($db);
});

test('F-P17-c', 'Elle girilmiş değerlere dokunulmaz', function (): void {
    $db = arc_need_db();

    $db->update('settings', ['value' => 'Cumhuriyet Mah. 12. Sk. No: 3'], ['key' => 'nap_street']);
    $db->update('settings', ['value' => '+90 266 000 00 00'], ['key' => 'nap_phone']);
    $db->update('settings', ['value' => 'merhaba@baskaadres.com'], ['key' => 'nap_email']);
    $db->update('settings', ['value' => 'İşletmenin kendi yazdığı not.'], ['key' => 'projects_notice']);
    Settings::flush();

    arc_run_migration($db);

    assertSame('Cumhuriyet Mah. 12. Sk. No: 3', arc_setting_row($db, 'nap_street'), 'Özel adres korunmalı');
    assertSame('+90 266 000 00 00', arc_setting_row($db, 'nap_phone'), 'Özel telefon korunmalı');
    assertSame('merhaba@baskaadres.com', arc_setting_row($db, 'nap_email'), 'Özel e-posta korunmalı');
    assertSame('İşletmenin kendi yazdığı not.', arc_setting_row($db, 'projects_notice'), 'Özel not korunmalı');

    arc_restore_seed_state($db);
});

test('F-P17-d', 'Çevirisi olmayan dil kapanır, çevirisi olan açık kalır', function (): void {
    $db = arc_need_db();
    arc_activate_langs(['tr', 'en', 'de']);

    $pageId = $db->insert('pages', ['type' => 'page', 'template' => 'page', 'status' => 'published']);
    $db->insert('page_translations', [
        'page_id'    => $pageId,
        'lang'       => 'de',
        'title'      => 'Beispielseite',
        'slug'       => 'beispielseite',
        'content'    => '<p>Übersetzter Inhalt.</p>',
        'word_count' => 3,
    ]);

    arc_run_migration($db);

    $langs = Lang::allLanguages();
    assertSame(1, (int) $langs['tr']['is_active'], 'Varsayılan dil her zaman açık kalmalı');
    assertSame(1, (int) $langs['de']['is_active'], 'Çevirisi girilmiş dil kapatılmamalı');
    assertSame(0, (int) $langs['en']['is_active'], 'Tek çevirisi olmayan dil yayından kalkmalı');

    // Ceviri satiri sayfa ile birlikte dusuyor (ON DELETE CASCADE).
    $db->delete('pages', ['id' => $pageId]);
    arc_restore_seed_state($db);
});

test('F-P17-e', 'Göç iki kez uygulanabilir', function (): void {
    $db = arc_need_db();

    $db->update('settings', ['value' => 'Tuzcumurat Mah. 27016 Sk. Uysal Apt. No: 5 Kat: 3 Daire: 8'], ['key' => 'nap_street']);
    $db->delete('settings', ['key' => 'projects_notice']);
    Settings::flush();

    arc_run_migration($db);
    $firstStreet = arc_setting_row($db, 'nap_street');
    $firstNotice = arc_setting_row($db, 'projects_notice');

    // Ikinci kez calistirmak hicbir degeri degistirmemeli; goc kaydi silinmis
    // ya da elle yeniden calistirilmis bir kurulum da bozulmamali.
    arc_run_migration($db);

    assertSame($firstStreet, arc_setting_row($db, 'nap_street'), 'Adres ikinci geçişte değişmemeli');
    assertSame($firstNotice, arc_setting_row($db, 'projects_notice'), 'Not ikinci geçişte kopyalanmamalı');
    assertSame(1, $db->count('settings', ['key' => 'projects_notice']), 'Not tek satır olmalı');

    arc_restore_seed_state($db);
});
