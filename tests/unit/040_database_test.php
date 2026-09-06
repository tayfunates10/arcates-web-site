<?php
/**
 * Database birim testleri.  DOCS.md 14.2 (U-07), 10.1
 */

declare(strict_types=1);

use Arcates\Core\Database;

test('U-07', 'Database::insert ile yazilan veri aynen geri okunur', function (): void {
    $db = arc_need_db();

    $id = $db->insert('projects', [
        'client_name' => 'Zeytin Kooperatifi Ç.Ğ.İ.Ö.Ş.Ü.',
        'sector'      => 'Zeytinyagi',
        'district'    => 'Edremit',
        'live_url'    => 'https://ornek.test',
        'status'      => 'draft',
        'sort'        => 3,
    ]);

    assertGreaterThan(0, $id, 'Yeni kimlik donmeli');

    $row = $db->first('SELECT * FROM projects WHERE id = :id', [':id' => $id]);
    assertSame('Zeytin Kooperatifi Ç.Ğ.İ.Ö.Ş.Ü.', $row['client_name'], 'Turkce karakter bozulmamali');
    assertSame('Edremit', $row['district']);
    assertSame(3, (int) $row['sort']);

    $db->delete('projects', ['id' => $id]);
    assertSame(0, $db->count('projects', ['id' => $id]), 'Silinen kayit kalmamali');
});

test('U-07b', 'Gecersiz tablo veya sutun adi SQL\'e giremez', function (): void {
    assertThrows(RuntimeException::class, static function (): void {
        Database::identifier('users; DROP TABLE users');
    }, 'Noktali virgullu ad reddedilmeli');

    assertThrows(RuntimeException::class, static function (): void {
        Database::identifier('users`');
    }, 'Ters tirnakli ad reddedilmeli');

    assertThrows(RuntimeException::class, static function (): void {
        Database::identifier('');
    }, 'Bos ad reddedilmeli');

    assertSame('page_translations', Database::identifier('page_translations'), 'Gecerli ad gecmeli');
});

test('U-07c', 'Kosulsuz silme veya guncelleme reddedilir', function (): void {
    $db = arc_need_db();

    assertThrows(RuntimeException::class, static function () use ($db): void {
        $db->delete('projects', []);
    }, 'Kosulsuz DELETE reddedilmeli');

    assertThrows(RuntimeException::class, static function () use ($db): void {
        $db->update('projects', ['sort' => 1], []);
    }, 'Kosulsuz UPDATE reddedilmeli');
});

test('U-07d', 'PDO ayarlari sartnamedeki gibi', function (): void {
    $db  = arc_need_db();
    $pdo = $db->pdo();

    assertSame(PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(PDO::ATTR_ERRMODE), 'ERRMODE_EXCEPTION olmali');
    assertSame(PDO::FETCH_ASSOC, $pdo->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE), 'FETCH_ASSOC olmali');
    assertFalse((bool) $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES), 'EMULATE_PREPARES kapali olmali');
});

test('U-07e', 'Goc betigi ifadelere dogru ayrilir', function (): void {
    $sql = "-- yorum satiri; noktali virgul iceriyor\n"
        . "CREATE TABLE a (x VARCHAR(10) DEFAULT 'bir;iki');\n"
        . "/* blok yorum; */\n"
        . "INSERT INTO a (x) VALUES ('uc;dort');";

    $statements = Arcates\Core\Migrator::splitStatements($sql);

    assertCount(2, $statements, 'Iki ifade cikmali');
    assertContains('CREATE TABLE a', $statements[0]);
    assertContains("'uc;dort'", $statements[1], 'Dize icindeki noktali virgul ayirici sayilmamali');
});
