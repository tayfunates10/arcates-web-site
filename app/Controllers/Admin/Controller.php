<?php
/**
 * Panel denetleyicileri icin temel sinif.
 *
 * Oturum kontrolu, rol kontrolu, CSRF dogrulamasi ve duzen icinde isleme
 * burada toplanir; her denetleyici bu davranisi devralir.
 * DOCS.md 9, 10.5, 10.7
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Auth;
use Arcates\Core\Database;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Session;
use Arcates\Core\View;

abstract class Controller
{
    protected ?string $ability = null;
    protected string $section = '';

    protected function db(): Database
    {
        return Database::instance();
    }

    protected function guard(): ?Response
    {
        if (!Auth::check()) {
            Session::set('_intended', $_SERVER['REQUEST_URI'] ?? admin_url());
            return Response::redirect(admin_url('giris'));
        }

        if ($this->ability !== null && !Auth::can($this->ability)) {
            Logger::activity('access.denied', 'admin', null, $this->ability);
            return Response::html(View::render('errors/403'), 403);
        }

        return null;
    }

    protected function guardAdmin(): ?Response
    {
        $guard = $this->guard();
        if ($guard !== null) {
            return $guard;
        }

        if (!Auth::isAdmin()) {
            Logger::activity('access.denied', 'admin', null, 'admin rolü gerekli');
            return Response::html(View::render('errors/403', [
                'message' => 'Bu bölüm yalnızca yönetici rolüne açıktır.',
            ]), 403);
        }

        return null;
    }

    protected function verifyCsrf(Request $request): ?Response
    {
        if (Security::csrfCheck((string) $request->post('_token'))) {
            return null;
        }

        Logger::activity('csrf.fail', 'admin', null, $request->path());
        return Response::html(View::render('errors/419'), 419);
    }

    protected function view(string $template, array $data = [], int $status = 200): Response
    {
        $data += ['title' => '', 'section' => $this->section];
        $data['_errors'] = $data['_errors'] ?? Session::takeErrors();
        $data['_old']    = $data['_old'] ?? Session::takeOld();
        $data['_flash']  = Session::takeFlash();
        $data['_user']   = Auth::user();
        $data['_menu']   = self::menu();

        View::shareMany(['_errors' => $data['_errors'], '_old' => $data['_old']]);
        return Response::html(View::renderIn('admin/layout', 'admin/' . $template, $data), $status);
    }

    protected function back(string $to, string $type = 'success', string $message = ''): Response
    {
        if ($message !== '') {
            Session::flash($type, $message);
        }
        return Response::redirect($to);
    }

    protected function withErrors(string $to, array $errors, array $old = []): Response
    {
        Session::flashErrors($errors, $old);
        Session::flash('error', 'Formda eksik veya hatalı alanlar var.');
        return Response::redirect($to);
    }

    public static function menu(): array
    {
        $items = [
            ['key' => 'dashboard',   'label' => 'Pano',            'url' => admin_url(),                  'ability' => null,                  'icon' => 'grid'],
            ['key' => 'home',        'label' => 'Anasayfa',        'url' => admin_url('anasayfa'),        'ability' => 'home.edit',           'icon' => 'home'],
            ['key' => 'pages',       'label' => 'Sayfalar',        'url' => admin_url('sayfalar'),        'ability' => 'pages.edit',          'icon' => 'file'],
            ['key' => 'projects',    'label' => 'Örnek siteler',   'url' => admin_url('referanslar'),     'ability' => 'projects.edit',       'icon' => 'star'],
            ['key' => 'posts',       'label' => 'Blog',            'url' => admin_url('blog'),            'ability' => 'posts.edit',          'icon' => 'pen'],
            ['key' => 'faqs',        'label' => 'SSS',             'url' => admin_url('sss'),             'ability' => 'faqs.edit',           'icon' => 'help'],
            ['key' => 'media',       'label' => 'Medya',           'url' => admin_url('medya'),           'ability' => 'media.edit',          'icon' => 'image'],
            ['key' => 'menus',       'label' => 'Menüler',         'url' => admin_url('menuler'),         'ability' => 'pages.edit',          'icon' => 'list'],
            ['key' => 'seo',         'label' => 'SEO',             'url' => admin_url('seo'),             'ability' => 'seo.view',            'icon' => 'search'],
            ['key' => 'redirects',   'label' => 'Yönlendirmeler',  'url' => admin_url('yonlendirmeler'),  'ability' => 'redirects.manage',    'icon' => 'arrow'],
            ['key' => 'submissions', 'label' => 'Formlar',         'url' => admin_url('formlar'),         'ability' => 'submissions.manage',  'icon' => 'inbox'],
            ['key' => 'stats',       'label' => 'İstatistik',      'url' => admin_url('istatistik'),      'ability' => 'stats.view',          'icon' => 'chart'],
            ['key' => 'users',       'label' => 'Kullanıcılar',    'url' => admin_url('kullanicilar'),    'ability' => 'users.manage',        'icon' => 'users'],
            ['key' => 'settings',    'label' => 'Ayarlar',         'url' => admin_url('ayarlar'),         'ability' => 'settings.manage',     'icon' => 'gear'],
            ['key' => 'backups',     'label' => 'Yedekleme',       'url' => admin_url('yedekleme'),       'ability' => 'backups.manage',      'icon' => 'save'],
            ['key' => 'activity',    'label' => 'İşlem günlüğü',   'url' => admin_url('islem-gunlugu'),   'ability' => 'activity.view',       'icon' => 'clock'],
        ];

        return array_values(array_filter($items, static function (array $item): bool {
            return $item['ability'] === null || Auth::can($item['ability']);
        }));
    }
}
