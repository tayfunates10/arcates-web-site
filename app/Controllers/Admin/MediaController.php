<?php
/**
 * Medya kitapligi.
 *
 * Coklu yukleme, izgara gorunum, alt metin alani (bossa uyari rozeti),
 * varyant bilgisi, kullanim yeri gosterimi, kullanimdaki dosya icin silme
 * uyarisi.  DOCS.md 9.5 — testler F-06, F-07, S-05, S-06
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Lang;
use Arcates\Core\Logger;
use Arcates\Core\Media;
use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Session;

final class MediaController extends Controller
{
    protected string $section = 'media';
    protected ?string $ability = 'media.edit';

    private const PER_PAGE = 48;

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $page   = max(1, $request->int('sayfa', 1));
        $offset = ($page - 1) * self::PER_PAGE;
        $lang   = Lang::defaultCode();

        $total = (int) $this->db()->count('media');

        $rows = $this->db()->all(
            'SELECT m.*, t.alt, t.title AS alt_title
               FROM media m
               LEFT JOIN media_translations t ON t.media_id = m.id AND t.lang = :lang
              ORDER BY m.created_at DESC, m.id DESC
              LIMIT ' . self::PER_PAGE . ' OFFSET ' . $offset,
            [':lang' => $lang]
        );

        foreach ($rows as &$row) {
            $row['variants'] = Media::decodeVariants($row['variants'] ?? null);
        }
        unset($row);

        return $this->view('media/index', [
            'title'   => 'Medya',
            'items'   => $rows,
            'total'   => $total,
            'page'    => $page,
            'pages'   => (int) ceil($total / self::PER_PAGE),
            'lang'    => $lang,
            'langs'   => Lang::languages(),
            'maxSize' => (int) config('upload.max_size', 5 * 1024 * 1024),
            'allowed' => Media::allowedExtensions(),
        ]);
    }

    /** Coklu yukleme. */
    public function upload(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $files = $request->file('files');
        if ($files === null || !isset($files['name'])) {
            return $this->back(admin_url('medya'), 'error', 'Dosya seçilmedi.');
        }

        $names   = is_array($files['name']) ? $files['name'] : [$files['name']];
        $ok      = 0;
        $errors  = [];

        foreach (array_keys($names) as $index) {
            $single = [
                'name'     => is_array($files['name']) ? $files['name'][$index] : $files['name'],
                'type'     => is_array($files['type']) ? $files['type'][$index] : $files['type'],
                'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$index] : $files['tmp_name'],
                'error'    => is_array($files['error']) ? $files['error'][$index] : $files['error'],
                'size'     => is_array($files['size']) ? $files['size'][$index] : $files['size'],
            ];

            if ((int) $single['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $result = Media::store($single, \Arcates\Core\Auth::id());

            if ($result['ok']) {
                $ok++;
                Logger::activity('media.upload', 'media', $result['id'], (string) $single['name']);
            } else {
                $errors[] = $single['name'] . ': ' . $result['error'];
                Logger::activity('media.reject', 'media', null, $single['name'] . ' — ' . $result['error']);
            }
        }

        if ($ok > 0) {
            Session::flash('success', $ok . ' dosya yüklendi.');
        }
        foreach (array_slice($errors, 0, 5) as $error) {
            Session::flash('error', $error);
        }

        return Response::redirect(admin_url('medya'));
    }

    /** Alt metni ve basligi kaydeder. */
    public function update(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id = (int) ($params['id'] ?? 0);
        if (Media::find($id) === null) {
            return $this->back(admin_url('medya'), 'error', 'Dosya bulunamadı.');
        }

        foreach ($request->arr('alt') as $lang => $text) {
            if (!Lang::exists((string) $lang)) {
                continue;
            }
            $titles = $request->arr('alt_title');
            Media::setAlt($id, (string) $lang, (string) $text, (string) ($titles[$lang] ?? ''));
        }

        Logger::activity('media.update', 'media', $id);

        return $this->back(admin_url('medya'), 'success', 'Alt metni kaydedildi.');
    }

    /** Kullanim yeri ayrintisi. */
    public function show(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $id    = (int) ($params['id'] ?? 0);
        $media = Media::find($id);

        if ($media === null) {
            return $this->back(admin_url('medya'), 'error', 'Dosya bulunamadı.');
        }

        return $this->view('media/show', [
            'title'  => 'Dosya ayrıntısı',
            'media'  => $media,
            'usage'  => Media::usage($id),
            'alts'   => $this->db()->all('SELECT lang, alt, title FROM media_translations WHERE media_id = :id', [':id' => $id]),
            'langs'  => Lang::languages(),
        ]);
    }

    /**
     * Siler. Kullanimdaki dosya icin once uyari verilir; onay kutusu
     * isaretlenmeden silinmez.  DOCS.md 9.5, test F-07
     */
    public function destroy(Request $request, array $params): Response
    {
        if ($guard = $this->guard()) {
            return $guard;
        }
        if ($csrf = $this->verifyCsrf($request)) {
            return $csrf;
        }

        $id    = (int) ($params['id'] ?? 0);
        $media = Media::find($id);

        if ($media === null) {
            return $this->back(admin_url('medya'), 'error', 'Dosya bulunamadı.');
        }

        $usage = Media::usage($id);

        if ($usage !== [] && !$request->bool('force')) {
            $labels = array_slice(array_map(
                static fn (array $u): string => $u['type'] . ': ' . $u['label'],
                $usage
            ), 0, 4);

            return $this->back(
                admin_url('medya/' . $id),
                'warning',
                'Bu dosya ' . count($usage) . ' yerde kullanılıyor (' . implode(', ', $labels)
                . '). Silmek için ayrıntı ekranındaki onayı işaretleyin.'
            );
        }

        Media::delete($id);
        Logger::activity('media.delete', 'media', $id, (string) $media['filename']);

        return $this->back(admin_url('medya'), 'success', 'Dosya silindi.');
    }
}
