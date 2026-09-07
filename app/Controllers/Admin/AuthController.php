<?php
/**
 * Panel girisi ve cikisi.
 *
 * DOCS.md 10.3, 10.4, 10.5 — testler S-03, S-04, S-08, S-09
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Auth;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Session;
use Arcates\Core\Validator;
use Arcates\Core\View;

final class AuthController extends Controller
{
    /** Giris ekrani. */
    public function showLogin(Request $request, array $params): Response
    {
        if (Auth::check()) {
            return Response::redirect(admin_url());
        }

        return Response::html(View::render('admin/login', [
            'errors' => Session::takeErrors(),
            'old'    => Session::takeOld(),
            'flash'  => Session::takeFlash(),
        ]));
    }

    /** Giris gonderimi. */
    public function login(Request $request, array $params): Response
    {
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $validator = new Validator($request->allPost(), [
            'email'    => 'E-posta',
            'password' => 'Şifre',
        ]);
        $validator->required('email')->email('email')->required('password');

        if ($validator->fails()) {
            return $this->withErrors(admin_url('giris'), $validator->firstErrors(), [
                'email' => $request->str('email'),
            ]);
        }

        $email  = $request->str('email');
        $result = Auth::attempt($email, (string) $request->post('password'), $request->ip());

        if (!$result['ok']) {
            // Kilit ve gecersiz bilgi ayni ekranda, ayni gecikmeyle bildirilir;
            // hesabin var olup olmadigi sizdirilmaz. DOCS.md 10.4
            $message = $result['reason'] === 'locked'
                ? 'Çok fazla başarısız deneme yapıldı. ' . ceil($result['wait'] / 60) . ' dakika sonra tekrar deneyin.'
                : 'E-posta veya şifre hatalı.';

            return $this->withErrors(admin_url('giris'), ['email' => $message], ['email' => $email]);
        }

        Auth::clearAttempts($request->ip(), $email);

        $intended = Session::get('_intended');
        Session::forget('_intended');

        $target = is_string($intended) && str_starts_with($intended, admin_url())
            ? $intended
            : admin_url();

        Session::flash('success', 'Hoş geldiniz.');
        return Response::redirect($target);
    }

    /** Cikis. Yalnizca POST ile; CSRF zorunlu. */
    public function logout(Request $request, array $params): Response
    {
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        Auth::logout();
        Session::start();
        Session::flash('info', 'Oturumunuz kapatıldı.');

        return Response::redirect(admin_url('giris'));
    }
}
