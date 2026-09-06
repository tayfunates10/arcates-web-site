<?php
/**
 * Kullanici yonetimi.
 *
 * Admin tam yetkili, editor yalnizca icerik. Bu ekran yalnizca yoneticiye
 * aciktir.  DOCS.md 9.11, test S-10
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Auth;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Validator;

final class UserController extends Controller
{
    protected string $section = 'users';
    protected ?string $ability = 'users.manage';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        return $this->view('users/index', [
            'title' => 'Kullanicilar',
            'users' => $this->db()->all(
                'SELECT id, name, email, role, status, last_login_at, created_at FROM users ORDER BY created_at'
            ),
        ]);
    }

    public function create(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        return $this->view('users/form', [
            'title' => 'Yeni kullanici',
            'user'  => null,
        ]);
    }

    public function edit(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $user = $this->db()->first(
            'SELECT id, name, email, role, status FROM users WHERE id = :id',
            [':id' => (int) ($params['id'] ?? 0)]
        );

        if ($user === null) {
            return $this->back(admin_url('kullanicilar'), 'error', 'Kullanici bulunamadi.');
        }

        return $this->view('users/form', [
            'title' => 'Kullaniciyi duzenle',
            'user'  => $user,
        ]);
    }

    public function store(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id  = (int) ($params['id'] ?? 0);
        $url = $id > 0 ? admin_url('kullanicilar/' . $id) : admin_url('kullanicilar/yeni');

        $validator = new Validator($request->allPost(), [
            'name'  => 'Ad soyad',
            'email' => 'E-posta',
        ]);

        $validator->required('name')->max('name', 120)
            ->required('email')->email('email')
            ->in('role', ['admin', 'editor']);

        $password = (string) $request->post('password', '');

        // Yeni kayitta sifre zorunlu; duzenlemede bos birakilirsa degismez.
        if ($id === 0 || $password !== '') {
            $validator->required('password')->password('password')
                ->matches('password_confirm', 'password', 'Sifreler birbiriyle ayni degil.');
        }

        $email = mb_strtolower($request->str('email'));

        $taken = $this->db()->first(
            'SELECT id FROM users WHERE email = :email AND id <> :id',
            [':email' => $email, ':id' => $id]
        );
        if ($taken !== null) {
            $validator->addError('email', 'Bu e-posta adresi baska bir kullaniciya ait.');
        }

        if ($validator->fails()) {
            return $this->withErrors($url, $validator->firstErrors(), [
                'name'  => $request->str('name'),
                'email' => $email,
                'role'  => $request->str('role'),
            ]);
        }

        $data = [
            'name'   => $request->str('name'),
            'email'  => $email,
            'role'   => $request->str('role') === 'admin' ? 'admin' : 'editor',
            'status' => $request->bool('status') ? 1 : 0,
        ];

        if ($password !== '') {
            $data['password_hash'] = Auth::hash($password);
        }

        if ($id > 0) {
            // Son yoneticinin rolu dusurulemez veya hesabi kapatilamaz.
            if ($this->wouldRemoveLastAdmin($id, $data['role'], (int) $data['status'])) {
                return $this->back($url, 'error', 'Sistemde en az bir etkin yonetici kalmalidir.');
            }

            $this->db()->update('users', $data, ['id' => $id]);
            Logger::activity('user.update', 'user', $id, $data['email']);
            return $this->back(admin_url('kullanicilar'), 'success', 'Kullanici guncellendi.');
        }

        $newId = $this->db()->insert('users', $data);
        Logger::activity('user.create', 'user', $newId, $data['email']);

        return $this->back(admin_url('kullanicilar'), 'success', 'Kullanici olusturuldu.');
    }

    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id = (int) ($params['id'] ?? 0);

        if ($id === Auth::id()) {
            return $this->back(admin_url('kullanicilar'), 'error', 'Kendi hesabinizi silemezsiniz.');
        }

        if ($this->wouldRemoveLastAdmin($id, 'editor', 0)) {
            return $this->back(admin_url('kullanicilar'), 'error', 'Sistemde en az bir etkin yonetici kalmalidir.');
        }

        $this->db()->delete('users', ['id' => $id]);
        Logger::activity('user.delete', 'user', $id);

        return $this->back(admin_url('kullanicilar'), 'success', 'Kullanici silindi.');
    }

    /** Kendi sifresini degistirme ekrani. DOCS.md 9.11 */
    public function profile(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        return $this->view('users/profile', [
            'title' => 'Hesabim',
            'user'  => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $user = Auth::user();
        if ($user === null) {
            return Response::redirect(admin_url('giris'));
        }

        $validator = new Validator($request->allPost(), ['name' => 'Ad soyad']);
        $validator->required('name')->max('name', 120);

        $current = (string) $request->post('current_password', '');
        $new     = (string) $request->post('password', '');

        if ($new !== '') {
            $validator->password('password')
                ->matches('password_confirm', 'password', 'Sifreler birbiriyle ayni degil.');

            $hash = (string) $this->db()->value(
                'SELECT password_hash FROM users WHERE id = :id',
                [':id' => (int) $user['id']]
            );

            if (!password_verify($current, $hash)) {
                $validator->addError('current_password', 'Mevcut sifre hatali.');
            }
        }

        if ($validator->fails()) {
            return $this->withErrors(admin_url('hesabim'), $validator->firstErrors(), [
                'name' => $request->str('name'),
            ]);
        }

        $data = ['name' => $request->str('name')];
        if ($new !== '') {
            $data['password_hash'] = Auth::hash($new);
        }

        $this->db()->update('users', $data, ['id' => (int) $user['id']]);
        Logger::activity('user.profile', 'user', (int) $user['id']);
        Auth::forget();

        return $this->back(admin_url('hesabim'), 'success', 'Hesap bilgileri guncellendi.');
    }

    /** Bu degisiklik son etkin yoneticiyi ortadan kaldirir mi? */
    private function wouldRemoveLastAdmin(int $userId, string $newRole, int $newStatus): bool
    {
        $current = $this->db()->first('SELECT role, status FROM users WHERE id = :id', [':id' => $userId]);
        if ($current === null || $current['role'] !== 'admin' || (int) $current['status'] !== 1) {
            return false;
        }

        if ($newRole === 'admin' && $newStatus === 1) {
            return false;
        }

        $others = (int) $this->db()->value(
            'SELECT COUNT(*) FROM users WHERE role = :role AND status = 1 AND id <> :id',
            [':role' => 'admin', ':id' => $userId]
        );

        return $others === 0;
    }
}
