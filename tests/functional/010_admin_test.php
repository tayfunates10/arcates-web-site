<?php
/**
 * Panel iskeleti — oturum ve rol kontrolu.
 * DOCS.md 9, 9.11 — testler S-09, S-10
 */

declare(strict_types=1);

use Arcates\Controllers\Admin\ActivityController;
use Arcates\Controllers\Admin\DashboardController;
use Arcates\Controllers\Admin\SettingController;
use Arcates\Controllers\Admin\UserController;
use Arcates\Core\Auth;
use Arcates\Core\Database;
use Arcates\Core\Request;
use Arcates\Core\Session;

/** Belirtilen rolde bir kullanici olusturur ve oturum acar. */
function arc_login_as(Database $db, string $role): int
{
    $email = $role . '@panel.test';
    $db->run('DELETE FROM users WHERE email = :email', [':email' => $email]);

    $id = $db->insert('users', [
        'name'          => ucfirst($role) . ' Kullanicisi',
        'email'         => $email,
        'password_hash' => Auth::hash('panel-sifresi-2026'),
        'role'          => $role,
        'status'        => 1,
    ]);

    arc_reset_session();
    Auth::forget();
    Session::set('user_id', $id);

    return $id;
}

function arc_logout_test(): void
{
    Auth::forget();
    arc_reset_session();
}

test('S-09', 'Oturumsuz panel istegi giris ekranina yonlendirir', function (): void {
    arc_need_db();
    arc_logout_test();

    $dashboard = (new DashboardController())->index(Request::make('GET', admin_url()), []);
    assertSame(302, $dashboard->status(), 'Yonlendirme donmeli');
    assertContains('/giris', (string) $dashboard->headerLine('Location'), 'Giris ekranina gitmeli');

    $pages = (new UserController())->index(Request::make('GET', admin_url('kullanicilar')), []);
    assertSame(302, $pages->status(), 'Kullanici listesi de korunmali');

    $activity = (new ActivityController())->index(Request::make('GET', admin_url('islem-gunlugu')), []);
    assertSame(302, $activity->status(), 'Islem gunlugu de korunmali');
});

test('S-10', 'Editor rolu kullanicilar ekranina erisemez', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'editor');

    $users = (new UserController())->index(Request::make('GET', admin_url('kullanicilar')), []);
    assertSame(403, $users->status(), 'Editor icin 403 donmeli');

    $settings = (new SettingController())->index(Request::make('GET', admin_url('ayarlar')), []);
    assertSame(403, $settings->status(), 'Ayarlar da kapali olmali');

    $activity = (new ActivityController())->index(Request::make('GET', admin_url('islem-gunlugu')), []);
    assertSame(403, $activity->status(), 'Islem gunlugu de kapali olmali');

    // Editor icerik yetkilerini tasir.
    assertTrue(Auth::can('pages.edit'), 'Editor sayfalari duzenleyebilmeli');
    assertFalse(Auth::can('users.manage'), 'Editor kullanici yonetemez');
    assertFalse(Auth::can('settings.manage'), 'Editor ayarlari degistiremez');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});

test('S-10b', 'Yonetici rolu tum ekranlari gorur', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'admin');

    $dashboard = (new DashboardController())->index(Request::make('GET', admin_url()), []);
    assertSame(200, $dashboard->status(), 'Pano acilmali');
    assertContains('Pano', $dashboard->body(), 'Pano basligi gorunmeli');

    $users = (new UserController())->index(Request::make('GET', admin_url('kullanicilar')), []);
    assertSame(200, $users->status(), 'Kullanici listesi acilmali');
    assertContains('admin@panel.test', $users->body(), 'Kendi hesabi listede gorunmeli');

    assertTrue(Auth::can('users.manage'));
    assertTrue(Auth::can('settings.manage'));

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});

test('F-P2-a', 'Panel menusu role gore kisilir', function (): void {
    $db = arc_need_db();

    $editorId = arc_login_as($db, 'editor');
    $editorKeys = array_column(Arcates\Controllers\Admin\Controller::menu(), 'key');
    assertFalse(in_array('users', $editorKeys, true), 'Editor menusunde kullanicilar olmamali');
    assertFalse(in_array('settings', $editorKeys, true), 'Editor menusunde ayarlar olmamali');
    assertTrue(in_array('pages', $editorKeys, true), 'Editor sayfalari gormeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $editorId]);

    $adminId = arc_login_as($db, 'admin');
    $adminKeys = array_column(Arcates\Controllers\Admin\Controller::menu(), 'key');
    assertTrue(in_array('users', $adminKeys, true), 'Yonetici menusunde kullanicilar olmali');
    assertTrue(in_array('settings', $adminKeys, true), 'Yonetici menusunde ayarlar olmali');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $adminId]);
});

test('F-P2-b', 'Son etkin yonetici rolu dusurulemez', function (): void {
    $db = arc_need_db();

    $db->run('DELETE FROM users');
    $adminId = arc_login_as($db, 'admin');

    $response = (new UserController())->store(
        Request::make('POST', admin_url('kullanicilar/' . $adminId), [
            '_token'   => Arcates\Core\Security::csrfToken(),
            'name'     => 'Admin Kullanicisi',
            'email'    => 'admin@panel.test',
            'role'     => 'editor',
            'status'   => '1',
        ]),
        ['id' => $adminId]
    );

    assertSame(302, $response->status(), 'Yonlendirme donmeli');

    $role = (string) $db->value('SELECT role FROM users WHERE id = :id', [':id' => $adminId]);
    assertSame('admin', $role, 'Son yoneticinin rolu degismemeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $adminId]);
});

test('F-P2-c', 'Islem gunlugu panel eylemlerini kaydeder', function (): void {
    $db = arc_need_db();
    $adminId = arc_login_as($db, 'admin');

    $db->run('DELETE FROM activity_log');

    (new UserController())->store(
        Request::make('POST', admin_url('kullanicilar/yeni'), [
            '_token'           => Arcates\Core\Security::csrfToken(),
            'name'             => 'Yeni Editor',
            'email'            => 'yeni.editor@panel.test',
            'role'             => 'editor',
            'status'           => '1',
            'password'         => 'yeni-sifre-2026',
            'password_confirm' => 'yeni-sifre-2026',
        ]),
        []
    );

    $log = $db->first('SELECT action, entity, user_id FROM activity_log ORDER BY id DESC LIMIT 1');
    assertTrue($log !== null, 'Gunluk kaydi olusmali');
    assertSame('user.create', $log['action'], 'Islem adi kaydedilmeli');
    assertSame('user', $log['entity']);
    assertSame($adminId, (int) $log['user_id'], 'Islemi yapan kullanici kaydedilmeli');

    arc_logout_test();
    $db->run('DELETE FROM activity_log');
    $db->run('DELETE FROM users');
});
