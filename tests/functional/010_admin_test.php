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
        'name'          => ucfirst($role) . ' Kullanıcısı',
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

test('S-09', 'Oturumsuz panel isteği giriş ekranına yönlendirir', function (): void {
    arc_need_db();
    arc_logout_test();

    $dashboard = (new DashboardController())->index(Request::make('GET', admin_url()), []);
    assertSame(302, $dashboard->status(), 'Yönlendirme dönmeli');
    assertContains('/giris', (string) $dashboard->headerLine('Location'), 'Giriş ekranına gitmeli');

    $pages = (new UserController())->index(Request::make('GET', admin_url('kullanicilar')), []);
    assertSame(302, $pages->status(), 'Kullanıcı listesi de korunmalı');

    $activity = (new ActivityController())->index(Request::make('GET', admin_url('islem-gunlugu')), []);
    assertSame(302, $activity->status(), 'İşlem günlüğü de korunmalı');
});

test('S-10', 'Editör rolü kullanıcılar ekranına erişemez', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'editor');

    $users = (new UserController())->index(Request::make('GET', admin_url('kullanicilar')), []);
    assertSame(403, $users->status(), 'Editör için 403 dönmeli');

    $settings = (new SettingController())->index(Request::make('GET', admin_url('ayarlar')), []);
    assertSame(403, $settings->status(), 'Ayarlar da kapalı olmalı');

    $activity = (new ActivityController())->index(Request::make('GET', admin_url('islem-gunlugu')), []);
    assertSame(403, $activity->status(), 'İşlem günlüğü de kapalı olmalı');

    // Editor icerik yetkilerini tasir.
    assertTrue(Auth::can('pages.edit'), 'Editör sayfaları düzenleyebilmeli');
    assertFalse(Auth::can('users.manage'), 'Editör kullanıcı yönetemez');
    assertFalse(Auth::can('settings.manage'), 'Editör ayarları değiştiremez');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});

test('S-10b', 'Yönetici rolü tüm ekranları görür', function (): void {
    $db = arc_need_db();
    $id = arc_login_as($db, 'admin');

    $dashboard = (new DashboardController())->index(Request::make('GET', admin_url()), []);
    assertSame(200, $dashboard->status(), 'Pano açılmalı');
    assertContains('Pano', $dashboard->body(), 'Pano başlığı görünmeli');

    $users = (new UserController())->index(Request::make('GET', admin_url('kullanicilar')), []);
    assertSame(200, $users->status(), 'Kullanıcı listesi açılmalı');
    assertContains('admin@panel.test', $users->body(), 'Kendi hesabı listede görünmeli');

    assertTrue(Auth::can('users.manage'));
    assertTrue(Auth::can('settings.manage'));

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});

test('F-P2-a', 'Panel menüsü role göre kısılır', function (): void {
    $db = arc_need_db();

    $editorId = arc_login_as($db, 'editor');
    $editorKeys = array_column(Arcates\Controllers\Admin\Controller::menu(), 'key');
    assertFalse(in_array('users', $editorKeys, true), 'Editör menüsünde kullanıcılar olmamalı');
    assertFalse(in_array('settings', $editorKeys, true), 'Editör menüsünde ayarlar olmamalı');
    assertTrue(in_array('pages', $editorKeys, true), 'Editör sayfaları görmeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $editorId]);

    $adminId = arc_login_as($db, 'admin');
    $adminKeys = array_column(Arcates\Controllers\Admin\Controller::menu(), 'key');
    assertTrue(in_array('users', $adminKeys, true), 'Yönetici menüsünde kullanıcılar olmalı');
    assertTrue(in_array('settings', $adminKeys, true), 'Yönetici menüsünde ayarlar olmalı');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $adminId]);
});

test('F-P2-b', 'Son etkin yönetici rolü düşürülemez', function (): void {
    $db = arc_need_db();

    $db->run('DELETE FROM users');
    $adminId = arc_login_as($db, 'admin');

    $response = (new UserController())->store(
        Request::make('POST', admin_url('kullanicilar/' . $adminId), [
            '_token'   => Arcates\Core\Security::csrfToken(),
            'name'     => 'Admin Kullanıcısı',
            'email'    => 'admin@panel.test',
            'role'     => 'editor',
            'status'   => '1',
        ]),
        ['id' => $adminId]
    );

    assertSame(302, $response->status(), 'Yönlendirme dönmeli');

    $role = (string) $db->value('SELECT role FROM users WHERE id = :id', [':id' => $adminId]);
    assertSame('admin', $role, 'Son yöneticinin rolü değişmemeli');

    arc_logout_test();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $adminId]);
});

test('F-P2-c', 'İşlem günlüğü panel eylemlerini kaydeder', function (): void {
    $db = arc_need_db();
    $adminId = arc_login_as($db, 'admin');

    $db->run('DELETE FROM activity_log');

    (new UserController())->store(
        Request::make('POST', admin_url('kullanicilar/yeni'), [
            '_token'           => Arcates\Core\Security::csrfToken(),
            'name'             => 'Yeni Editör',
            'email'            => 'yeni.editor@panel.test',
            'role'             => 'editor',
            'status'           => '1',
            'password'         => 'yeni-sifre-2026',
            'password_confirm' => 'yeni-sifre-2026',
        ]),
        []
    );

    $log = $db->first('SELECT action, entity, user_id FROM activity_log ORDER BY id DESC LIMIT 1');
    assertTrue($log !== null, 'Günlük kaydı oluşmalı');
    assertSame('user.create', $log['action'], 'İşlem adı kaydedilmeli');
    assertSame('user', $log['entity']);
    assertSame($adminId, (int) $log['user_id'], 'İşlemi yapan kullanıcı kaydedilmeli');

    arc_logout_test();
    $db->run('DELETE FROM activity_log');
    $db->run('DELETE FROM users');
});
