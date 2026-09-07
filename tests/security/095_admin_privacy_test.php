<?php
/**
 * Hassas panel bolumleri icin rol sinirlari.
 * Editor yalnizca icerik yonetir; form kayitlari ve yonlendirmeler admin'e ozeldir.
 */

declare(strict_types=1);

use Arcates\Core\Auth;
use Arcates\Core\Database;
use Arcates\Core\Request;
use Arcates\Core\Session;

function arc_privacy_login_editor(Database $db): int
{
    $email = 'privacy-editor@panel.test';
    $db->run('DELETE FROM users WHERE email = :email', [':email' => $email]);
    $id = $db->insert('users', [
        'name'          => 'Privacy Editor',
        'email'         => $email,
        'password_hash' => Auth::hash('guclu-sifre-2026'),
        'role'          => 'editor',
        'status'        => 1,
    ]);

    arc_reset_session();
    Auth::forget();
    Session::set('user_id', $id);
    return $id;
}

function arc_privacy_logout(): void
{
    Auth::forget();
    arc_reset_session();
}

test('S-19', 'Editör form kayıtlarına ve CSV dışa aktarmaya erişemez', function (): void {
    $db = arc_need_db();
    $id = arc_privacy_login_editor($db);

    assertFalse(Auth::can('submissions.manage'), 'Editör form yönetim yetkisi taşımamalı');

    $controller = new Arcates\Controllers\Admin\SubmissionController();
    $list = $controller->index(Request::make('GET', admin_url('formlar')), []);
    assertSame(403, $list->status(), 'Form listesi editöre 403 dönmeli');

    $export = $controller->export(Request::make('GET', admin_url('formlar/csv')), []);
    assertSame(403, $export->status(), 'CSV dışa aktarma editöre 403 dönmeli');

    arc_privacy_logout();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});

test('S-20', 'Editör yönlendirme ve 404 yönetimine erişemez', function (): void {
    $db = arc_need_db();
    $id = arc_privacy_login_editor($db);

    assertFalse(Auth::can('redirects.manage'), 'Editör yönlendirme yönetim yetkisi taşımamalı');

    $response = (new Arcates\Controllers\Admin\RedirectController())->index(
        Request::make('GET', admin_url('yonlendirmeler')),
        []
    );
    assertSame(403, $response->status(), 'Yönlendirme ekranı editöre 403 dönmeli');

    $keys = array_column(Arcates\Controllers\Admin\Controller::menu(), 'key');
    assertFalse(in_array('submissions', $keys, true), 'Editör menüsünde Formlar görünmemeli');
    assertFalse(in_array('redirects', $keys, true), 'Editör menüsünde Yönlendirmeler görünmemeli');

    arc_privacy_logout();
    $db->run('DELETE FROM users WHERE id = :id', [':id' => $id]);
});
