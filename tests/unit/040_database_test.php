<?php
/**
 * Database birim testleri.  DOCS.md 14.2 (U-07), 10.1
 */

declare(strict_types=1);

use Arcates\Core\Database;

test('U-07', 'Database::insert ile yazılan veri aynen geri okunur', function (): void {
    $db = arc_need_db();

    $id = $db->insert('projects', [
        'client_name' => 'Zeytin Kooperatifi Ç.Ğ.İ.Ö.Ş.Ü.',
        'sector'      => 'Zeytinyağı',
        'district'    => 'Edremit',
        'live_url'    => 'https://ornek.test',
        'status'      => 'draft',
        'sort'        => 3,
    ]);

    assertGreaterThan(0, $id, 'Yeni kimlik dönmeli');

    $row = $db->first('SELECT * FROM projects WHERE id = :id', [':id' => $id]);
    assertSame('Zeytin Kooperatifi Ç.Ğ.İ.Ö.Ş.Ü.', $row['client_name'], 'Türkçe karakter bozulmamalı');
    assertSame('Edremit', $row['district']);
    assertSame(3, (int) $row['sort']);

    $db->delete('projects', ['id' => $id]);
    assertSame(0, $db->count('projects', ['id' => $id]), 'Silinen kayıt kalmamalı');
});

test('U-07b', 'Geçersiz tablo veya sütun adı SQL\'e giremez', function (): void {
    assertThrows(RuntimeException::class, static function (): void {
        Database::identifier('users; DROP TABLE users');
    }, 'Noktalı virgüllü ad reddedilmeli');

    assertThrows(RuntimeException::class, static function (): void {
        Database::identifier('users`');
    }, 'Ters tırnaklı ad reddedilmeli');

    assertThrows(RuntimeException::class, static function (): void {
        Database::identifier('');
    }, 'Boş ad reddedilmeli');

    assertSame('page_translations', Database::identifier('page_translations'), 'Geçerli ad geçmeli');
});

test('U-07c', 'Koşulsuz silme veya güncelleme reddedilir', function (): void {
    $db = arc_need_db();

    assertThrows(RuntimeException::class, static function () use ($db): void {
        $db->delete('projects', []);
    }, 'Koşulsuz DELETE reddedilmeli');

    assertThrows(RuntimeException::class, static function () use ($db): void {
        $db->update('projects', ['sort' => 1], []);
    }, 'Koşulsuz UPDATE reddedilmeli');
});

test('U-07d', 'PDO ayarları şartnamedeki gibi', function (): void {
    $db  = arc_need_db();
    $pdo = $db->pdo();

    assertSame(PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(PDO::ATTR_ERRMODE), 'ERRMODE_EXCEPTION olmalı');
    assertSame(PDO::FETCH_ASSOC, $pdo->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE), 'FETCH_ASSOC olmalı');
    assertFalse((bool) $pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES), 'EMULATE_PREPARES kapalı olmalı');
});

test('U-07e', 'Göç betiği ifadelere doğru ayrılır', function (): void {
    $sql = "-- yorum satırı; noktalı virgül içeriyor\n"
        . "CREATE TABLE a (x VARCHAR(10) DEFAULT 'bir;iki');\n"
        . "/* blok yorum; */\n"
        . "INSERT INTO a (x) VALUES ('üç;dört');";

    $statements = Arcates\Core\Migrator::splitStatements($sql);

    assertCount(2, $statements, 'İki ifade çıkmalı');
    assertContains('CREATE TABLE a', $statements[0]);
    assertContains("'üç;dört'", $statements[1], 'Dize içindeki noktalı virgül ayırıcı sayılmamalı');
});
