<?php
/**
 * CSRF korumasi.  DOCS.md 10.5, testler S-03, S-04
 */

declare(strict_types=1);

use Arcates\Core\Security;

test('S-03', 'CSRF belirteci olmadan gonderim reddedilir', function (): void {
    arc_test_config();
    arc_reset_session();

    Security::csrfToken();                 // oturumda bir belirtec olusur

    assertFalse(Security::csrfCheck(null), 'Belirtecsiz gonderim reddedilmeli');
    assertFalse(Security::csrfCheck(''), 'Bos belirtec reddedilmeli');
});

test('S-04', 'Gecersiz CSRF belirteci reddedilir', function (): void {
    arc_test_config();
    arc_reset_session();

    $token = Security::csrfToken();

    assertTrue(Security::csrfCheck($token), 'Dogru belirtec kabul edilmeli');
    assertFalse(Security::csrfCheck(str_repeat('a', 64)), 'Yanlis belirtec reddedilmeli');
    assertFalse(Security::csrfCheck(substr($token, 0, -1)), 'Eksik belirtec reddedilmeli');
    assertFalse(Security::csrfCheck($token . 'x'), 'Fazladan karakterli belirtec reddedilmeli');
});

test('S-04b', 'Oturumda belirtec yokken hicbir deger kabul edilmez', function (): void {
    arc_test_config();
    arc_reset_session();

    assertFalse(Security::csrfCheck('herhangi-bir-deger'), 'Oturumsuz dogrulama gecmemeli');
});

test('S-04c', 'CSRF alani her POST formuna eklenebilir bicimde uretilir', function (): void {
    arc_test_config();
    arc_reset_session();

    $field = Security::csrfField();
    assertContains('name="_token"', $field, 'Alan adi _token olmali');
    assertContains('type="hidden"', $field, 'Alan gizli olmali');
    assertContains(Security::csrfToken(), $field, 'Alan gecerli belirteci tasimali');
});

test('S-04d', 'Kurulum ekrani gecersiz belirteci 419 ile karsilar', function (): void {
    arc_test_config();
    arc_reset_session();

    if (Arcates\Core\App::isInstalled()) {
        skip('Kurulum tamamlanmis; bu kontrol S-13 kapsaminda.');
    }

    $controller = new Arcates\Controllers\Front\InstallController();
    $response   = $controller->submit(
        Arcates\Core\Request::make('POST', '/install', ['action' => 'schema', '_token' => 'yanlis']),
        []
    );

    assertSame(419, $response->status(), 'Gecersiz belirtec 419 dondurmeli');
});
