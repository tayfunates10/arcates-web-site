<?php
/**
 * CSRF korumasi.  DOCS.md 10.5, testler S-03, S-04
 */

declare(strict_types=1);

use Arcates\Core\Security;

test('S-03', 'CSRF belirteci olmadan gönderim reddedilir', function (): void {
    arc_test_config();
    arc_reset_session();

    Security::csrfToken();                 // oturumda bir belirtec olusur

    assertFalse(Security::csrfCheck(null), 'Belirteçsiz gönderim reddedilmeli');
    assertFalse(Security::csrfCheck(''), 'Boş belirteç reddedilmeli');
});

test('S-04', 'Geçersiz CSRF belirteci reddedilir', function (): void {
    arc_test_config();
    arc_reset_session();

    $token = Security::csrfToken();

    assertTrue(Security::csrfCheck($token), 'Doğru belirteç kabul edilmeli');
    assertFalse(Security::csrfCheck(str_repeat('a', 64)), 'Yanlış belirteç reddedilmeli');
    assertFalse(Security::csrfCheck(substr($token, 0, -1)), 'Eksik belirteç reddedilmeli');
    assertFalse(Security::csrfCheck($token . 'x'), 'Fazladan karakterli belirteç reddedilmeli');
});

test('S-04b', 'Oturumda belirteç yokken hiçbir değer kabul edilmez', function (): void {
    arc_test_config();
    arc_reset_session();

    assertFalse(Security::csrfCheck('herhangi-bir-deger'), 'Oturumsuz doğrulama geçmemeli');
});

test('S-04c', 'CSRF alanı her POST formuna eklenebilir biçimde üretilir', function (): void {
    arc_test_config();
    arc_reset_session();

    $field = Security::csrfField();
    assertContains('name="_token"', $field, 'Alan adı _token olmalı');
    assertContains('type="hidden"', $field, 'Alan gizli olmalı');
    assertContains(Security::csrfToken(), $field, 'Alan geçerli belirteci taşımalı');
});

test('S-04d', 'Kurulum ekranı geçersiz belirteci 419 ile karşılar', function (): void {
    arc_test_config();
    arc_reset_session();

    if (Arcates\Core\App::isInstalled()) {
        skip('Kurulum tamamlanmış; bu kontrol S-13 kapsamında.');
    }

    $controller = new Arcates\Controllers\Front\InstallController();
    $response   = $controller->submit(
        Arcates\Core\Request::make('POST', '/install', ['action' => 'schema', '_token' => 'yanlis']),
        []
    );

    assertSame(419, $response->status(), 'Geçersiz belirteç 419 döndürmeli');
});
