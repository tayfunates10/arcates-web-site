<?php
/**
 * Giris guvenligi.  DOCS.md 10.1, 10.4 — testler S-01, S-08
 *
 * Veritabani yapilandirilmamissa atlanir.
 */

declare(strict_types=1);

use Arcates\Core\Auth;
use Arcates\Core\Database;

/** Testler icin bilinen bir hesap olusturur. */
function arc_seed_admin(Database $db, string $email = 'yonetici@ornek.test', string $password = 'guclu-sifre-2026'): int
{
    $db->run('DELETE FROM users WHERE email = :email', [':email' => $email]);

    return $db->insert('users', [
        'name'          => 'Test Yoneticisi',
        'email'         => $email,
        'password_hash' => Auth::hash($password),
        'role'          => 'admin',
        'status'        => 1,
    ]);
}

test('S-01', "Giris alanina ' OR '1'='1 yazmak basarisiz olur ve kaydedilir", function (): void {
    $db = arc_need_db();
    arc_reset_session();

    $userId = arc_seed_admin($db);
    $db->run('DELETE FROM login_attempts');

    $result = Auth::attempt("' OR '1'='1", "' OR '1'='1", '198.51.100.5');

    assertFalse($result['ok'], 'Enjeksiyon denemesi basarisiz olmali');
    assertSame('invalid', $result['reason']);
    assertSame(null, Arcates\Core\Session::get('user_id'), 'Oturum acilmamali');

    $attempts = (int) $db->value('SELECT COUNT(*) FROM login_attempts WHERE success = 0');
    assertGreaterThan(0, $attempts, 'Basarisiz deneme kaydedilmeli');

    // Tablolar yerinde durmali; enjeksiyon calismamis olmali.
    assertTrue($db->tableExists('users'), 'users tablosu yerinde olmali');
    assertGreaterThan(0, (int) $db->count('users', ['id' => $userId]), 'Hesap silinmemis olmali');

    $db->run('DELETE FROM users WHERE id = :id', [':id' => $userId]);
    $db->run('DELETE FROM login_attempts');
});

test('S-01b', 'Dogru bilgiyle giris calisir, kullanici var/yok ayrimi sizmaz', function (): void {
    $db = arc_need_db();
    arc_reset_session();

    $userId = arc_seed_admin($db);
    $db->run('DELETE FROM login_attempts');

    $wrongPassword = Auth::attempt('yonetici@ornek.test', 'yanlis-sifre-123', '198.51.100.6');
    $noSuchUser    = Auth::attempt('olmayan@ornek.test', 'yanlis-sifre-123', '198.51.100.6');

    assertSame($wrongPassword['reason'], $noSuchUser['reason'], 'Iki durumda ayni yanit donmeli');
    assertFalse($wrongPassword['ok']);
    assertFalse($noSuchUser['ok']);

    $ok = Auth::attempt('yonetici@ornek.test', 'guclu-sifre-2026', '198.51.100.7');
    assertTrue($ok['ok'], 'Dogru bilgiyle giris yapilmali');
    assertSame($userId, Arcates\Core\Session::get('user_id'), 'Oturuma kullanici yazilmali');

    Auth::forget();
    arc_reset_session();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $userId]);
    $db->run('DELETE FROM login_attempts');
});

test('S-08', '6. hatali giriste 15 dakika kilit devreye girer', function (): void {
    $db = arc_need_db();
    arc_reset_session();

    $userId = arc_seed_admin($db);
    $db->run('DELETE FROM login_attempts');

    $ip = '198.51.100.8';

    for ($i = 1; $i <= 5; $i++) {
        $result = Auth::attempt('yonetici@ornek.test', 'yanlis-sifre-' . $i, $ip);
        assertSame('invalid', $result['reason'], "Deneme {$i} gecersiz olmali, kilit degil");
    }

    $sixth = Auth::attempt('yonetici@ornek.test', 'guclu-sifre-2026', $ip);
    assertSame('locked', $sixth['reason'], '6. denemede kilit devreye girmeli');
    assertFalse($sixth['ok'], 'Dogru sifreyle bile giris yapilmamali');
    assertGreaterThan(0, $sixth['wait'], 'Bekleme suresi bildirilmeli');
    assertLessThan(901, $sixth['wait'], 'Bekleme suresi 15 dakikayi asmamali');

    Auth::forget();
    arc_reset_session();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $userId]);
    $db->run('DELETE FROM login_attempts');
});

test('S-08b', 'Kilit e-posta bazinda da sayilir', function (): void {
    $db = arc_need_db();
    arc_reset_session();

    $userId = arc_seed_admin($db);
    $db->run('DELETE FROM login_attempts');

    // Ayni e-posta, farkli IP adresleri.
    for ($i = 1; $i <= 5; $i++) {
        Auth::attempt('yonetici@ornek.test', 'yanlis-' . $i, '203.0.113.' . $i);
    }

    $state = Auth::lockState('203.0.113.99', 'yonetici@ornek.test');
    assertTrue($state['locked'], 'E-posta sayaci farkli IP\'den de kilitlemeli');

    $other = Auth::lockState('203.0.113.99', 'baska@ornek.test');
    assertFalse($other['locked'], 'Baska e-posta etkilenmemeli');

    Auth::forget();
    arc_reset_session();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $userId]);
    $db->run('DELETE FROM login_attempts');
});

test('S-08c', 'Sifreler password_hash ile saklanir', function (): void {
    $hash = Auth::hash('guclu-sifre-2026');

    assertNotSame('guclu-sifre-2026', $hash, 'Sifre duz metin saklanmamali');
    assertTrue(password_verify('guclu-sifre-2026', $hash), 'Dogrulama calismali');
    assertFalse(password_verify('baska-sifre-2026', $hash), 'Yanlis sifre dogrulanmamali');
    assertNotSame($hash, Auth::hash('guclu-sifre-2026'), 'Her karma farkli tuz kullanmali');
});
