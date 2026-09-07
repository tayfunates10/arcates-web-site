<?php
/**
 * Yedekleme.
 *
 * Elle yedek alma, son 10 yedek, indirme, gunluk otomatik yedek.
 * DOCS.md 9.11 — test F-19
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Backup;
use Arcates\Core\Logger;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Throwable;

final class BackupController extends Controller
{
    protected string $section = 'backups';
    protected ?string $ability = 'backups.manage';

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        return $this->view('backups/index', [
            'title'    => 'Yedekleme',
            'backups'  => Backup::listing(),
            'keep'     => Backup::KEEP,
            'writable' => is_writable(Backup::directory()),
        ]);
    }

    public function create(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        try {
            $filename = Backup::create();
            Logger::activity('backup.create', 'backup', null, $filename);
            return $this->back(admin_url('yedekleme'), 'success', 'Yedek alındı: ' . $filename);
        } catch (Throwable $e) {
            Logger::exception($e);
            return $this->back(admin_url('yedekleme'), 'error', 'Yedek alınamadı: ' . $e->getMessage());
        }
    }

    /** Yedek dosyasini indirir. */
    public function download(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $path = Backup::path((string) ($params['file'] ?? ''));
        if ($path === null) {
            return $this->back(admin_url('yedekleme'), 'error', 'Yedek bulunamadı.');
        }

        Logger::activity('backup.download', 'backup', null, basename($path));

        return new Response((string) file_get_contents($path), 200, [
            'Content-Type'        => 'application/gzip',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
            'Content-Length'      => (string) filesize($path),
        ]);
    }

    /**
     * Yedegi geri yukler.
     *
     * Geri yukleme mevcut veriyi degistirir; onay kutusu isaretlenmeden
     * calistirilmaz.  DOCS.md 9.11
     */
    public function restore(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        if (!$request->bool('confirm')) {
            return $this->back(admin_url('yedekleme'), 'error', 'Geri yükleme için onay kutusunu işaretleyin.');
        }

        $filename = (string) ($params['file'] ?? '');

        try {
            // Once mevcut durumun yedegi alinir; geri donus yolu acik kalsin.
            $safety = Backup::create();

            $statements = Backup::restore($filename);
            Logger::activity('backup.restore', 'backup', null, $filename . ' (' . $statements . ' ifade)');

            return $this->back(
                admin_url('yedekleme'),
                'success',
                'Yedek geri yüklendi. İşlem öncesi durum ' . $safety . ' dosyasına alındı.'
            );
        } catch (Throwable $e) {
            Logger::exception($e);
            return $this->back(admin_url('yedekleme'), 'error', 'Geri yükleme başarısız: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $filename = (string) ($params['file'] ?? '');

        if (Backup::delete($filename)) {
            Logger::activity('backup.delete', 'backup', null, $filename);
            return $this->back(admin_url('yedekleme'), 'success', 'Yedek silindi.');
        }

        return $this->back(admin_url('yedekleme'), 'error', 'Yedek bulunamadı.');
    }
}
