<?php
/**
 * Kurulum sihirbazi.
 *
 * Adimlar:  DOCS.md 13
 *   1. Gereksinim kontrolu (PHP surumu, eklentiler, yazilabilir klasorler)
 *   2. Veritabani baglanti testi ve `db/schema.sql` uygulamasi
 *   3. Ilk yonetici hesabi
 *   4. Varsayilan diller, ayarlar, anasayfa bolumleri; `installed.lock`
 *
 * Kurulum tamamlandiktan sonra bu denetleyici 404 dondurur.
 * DOCS.md 10.7, test S-13
 */

declare(strict_types=1);

namespace Arcates\Controllers\Front;

use Arcates\Core\App;
use Arcates\Core\Auth;
use Arcates\Core\Config;
use Arcates\Core\Database;
use Arcates\Core\Logger;
use Arcates\Core\Migrator;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;
use Arcates\Core\Seeder;
use Arcates\Core\Session;
use Arcates\Core\Validator;
use Arcates\Core\View;
use Throwable;

final class InstallController
{
    /** Kurulum ekrani. */
    public function index(Request $request, array $params): Response
    {
        if (App::isInstalled()) {
            return $this->closed();
        }

        $step = $this->currentStep();

        return Response::html(View::render('front/install', [
            'step'         => $step,
            'requirements' => $this->requirements(),
            'ready'        => $this->requirementsMet(),
            'dbState'      => $this->databaseState(),
            'errors'       => Session::takeErrors(),
            'old'          => Session::takeOld(),
            'flash'        => Session::takeFlash(),
        ]), 200);
    }

    /** Adim gonderimi. Her POST'ta CSRF dogrulanir. DOCS.md 10.5 */
    public function submit(Request $request, array $params): Response
    {
        if (App::isInstalled()) {
            return $this->closed();
        }

        if (!Security::csrfCheck((string) $request->post('_token'))) {
            Logger::activity('csrf.fail', 'install', null, $request->path());
            return Response::html(View::render('errors/419'), 419);
        }

        $action = $request->str('action');

        return match ($action) {
            'schema' => $this->applySchema($request),
            'admin'  => $this->createAdmin($request),
            default  => Response::redirect(path_url('/install')),
        };
    }

    // --- Adimlar ------------------------------------------------------------

    /** Sema uygulanir ve tohum verisi yazilir. */
    private function applySchema(Request $request): Response
    {
        if (!$this->requirementsMet()) {
            Session::flash('error', 'Once gereksinimleri karsilayin.');
            return Response::redirect(path_url('/install'));
        }

        try {
            $db = Database::instance();
            $db->connect();

            $migrator = new Migrator($db);
            $sql      = (string) file_get_contents(ARC_ROOT . '/db/schema.sql');
            $migrator->runSqlScript($sql);

            // Sifirdan kurulumda mevcut goc dosyalari uygulanmis sayilir;
            // sema zaten son hali icerir.  DOCS.md 8.6
            foreach ($migrator->pending() as $file) {
                $db->insert('migrations', ['filename' => $file]);
            }

            (new Seeder($db))->run();

            Session::flash('success', 'Veritabani hazirlandi. Simdi yonetici hesabini olusturun.');
        } catch (Throwable $e) {
            Logger::exception($e);
            Session::flash('error', 'Veritabani hazirlanamadi: ' . $e->getMessage());
        }

        return Response::redirect(path_url('/install'));
    }

    /** Ilk yonetici olusturulur ve kurulum kilitlenir. */
    private function createAdmin(Request $request): Response
    {
        $db = Database::instance();

        if (!$db->tableExists('users')) {
            Session::flash('error', 'Once veritabani semasini uygulayin.');
            return Response::redirect(path_url('/install'));
        }

        $validator = new Validator($request->allPost(), [
            'name'  => 'Ad soyad',
            'email' => 'E-posta',
        ]);

        $validator->required('name')->max('name', 120)
            ->required('email')->email('email')
            ->required('password')->password('password')
            ->matches('password_confirm', 'password', 'Sifreler birbiriyle ayni degil.');

        if ($validator->fails()) {
            Session::flashErrors($validator->firstErrors(), [
                'name'  => $request->str('name'),
                'email' => $request->str('email'),
            ]);
            return Response::redirect(path_url('/install'));
        }

        $email = mb_strtolower($request->str('email'));

        if ((int) $db->count('users') > 0) {
            Session::flash('error', 'Yonetici hesabi zaten olusturulmus.');
            return Response::redirect(path_url('/install'));
        }

        try {
            $userId = $db->insert('users', [
                'name'          => $request->str('name'),
                'email'         => $email,
                'password_hash' => Auth::hash((string) $request->post('password')),
                'role'          => 'admin',
                'status'        => 1,
            ]);

            // DOCS.md 13 adim 4 — kurulum kilidi.
            $lock = ARC_ROOT . '/storage/installed.lock';
            if (@file_put_contents($lock, date('c') . "\n") === false) {
                Session::flash('error', 'storage/ klasoru yazilabilir degil; installed.lock olusturulamadi.');
                return Response::redirect(path_url('/install'));
            }

            Logger::activity('install.complete', 'user', $userId, null, $userId, $request->ip());
            Session::flash('success', 'Kurulum tamamlandi. Panele giris yapabilirsiniz.');
        } catch (Throwable $e) {
            Logger::exception($e);
            Session::flash('error', 'Yonetici olusturulamadi: ' . $e->getMessage());
            return Response::redirect(path_url('/install'));
        }

        return Response::redirect(path_url(admin_url('giris')));
    }

    // --- Durum --------------------------------------------------------------

    private function currentStep(): int
    {
        if (!$this->requirementsMet()) {
            return 1;
        }

        $state = $this->databaseState();
        if (!$state['connected'] || !$state['schema']) {
            return 2;
        }

        return 3;
    }

    /**
     * Gereksinim listesi.
     *
     * @return array<int, array{label:string, ok:bool, detail:string}>
     */
    public function requirements(): array
    {
        $checks = [];

        $checks[] = [
            'label'  => 'PHP 8.1 veya ustu',
            'ok'     => PHP_VERSION_ID >= 80100,
            'detail' => PHP_VERSION,
        ];

        foreach (['pdo_mysql', 'mbstring', 'json', 'fileinfo'] as $ext) {
            $checks[] = [
                'label'  => 'Eklenti: ' . $ext,
                'ok'     => extension_loaded($ext),
                'detail' => extension_loaded($ext) ? 'yuklu' : 'eksik',
            ];
        }

        $image = extension_loaded('gd') || extension_loaded('imagick');
        $checks[] = [
            'label'  => 'Eklenti: gd veya imagick',
            'ok'     => $image,
            'detail' => $image ? 'yuklu' : 'eksik',
        ];

        foreach (['storage', 'storage/logs', 'storage/cache', 'storage/backups', 'public/uploads'] as $dir) {
            $path = ARC_ROOT . '/' . $dir;
            $checks[] = [
                'label'  => 'Yazilabilir: ' . $dir,
                'ok'     => is_dir($path) && is_writable($path),
                'detail' => is_dir($path) ? (is_writable($path) ? 'yazilabilir' : 'yazma izni yok') : 'klasor yok',
            ];
        }

        $checks[] = [
            'label'  => 'config/config.php mevcut',
            'ok'     => Config::exists(),
            'detail' => Config::exists() ? 'var' : 'config.example.php dosyasini kopyalayin',
        ];

        return $checks;
    }

    public function requirementsMet(): bool
    {
        foreach ($this->requirements() as $check) {
            if (!$check['ok']) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array{connected:bool, schema:bool, users:int, message:string}
     */
    public function databaseState(): array
    {
        $state = ['connected' => false, 'schema' => false, 'users' => 0, 'message' => ''];

        try {
            $db = Database::instance();
            $db->connect();
            $state['connected'] = true;
        } catch (Throwable $e) {
            $state['message'] = $e->getMessage();
            return $state;
        }

        $state['schema'] = $db->tableExists('users') && $db->tableExists('pages') && $db->tableExists('home_sections');
        if ($state['schema']) {
            $state['users'] = (int) $db->count('users');
        }

        return $state;
    }

    /** Kurulum kapali. DOCS.md 10.7, test S-13 */
    private function closed(): Response
    {
        return Response::html(View::render('errors/404', [
            'title'   => 'Kurulum kapali',
            'message' => 'Bu site zaten kurulmus. Kurulum sihirbazi erisime kapalidir.',
        ]), 404);
    }
}
