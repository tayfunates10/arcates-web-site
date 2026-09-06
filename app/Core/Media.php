<?php
/**
 * Medya yukleme ve goruntu isleme.
 *
 * DOCS.md 9.5, 10.6, 11.4
 *
 * Kurallar:
 *   Uzanti  jpg, jpeg, png, webp, gif, svg, pdf
 *   MIME    finfo_file ile dogrulanir, uzantiyla eslesmeli
 *   Boyut   5 MB
 *   Ad      bin2hex(random_bytes(8)); kullanicinin verdigi ad kullanilmaz
 *   SVG     script, on* nitelikleri, xlink:href temizlenir
 *   Klasor  uploads/YYYY/MM/, PHP calistirma kapali
 */

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;

final class Media
{
    /** Uzanti => kabul edilen MIME turleri. DOCS.md 10.6 */
    public const MIME_MAP = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
        'gif'  => ['image/gif'],
        'svg'  => ['image/svg+xml', 'text/plain', 'text/xml', 'application/xml'],
        'pdf'  => ['application/pdf'],
    ];

    /** Yeniden boyutlandirilabilen turler. */
    private const RASTER = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /** Yukleme kokune gore goreli klasor. */
    public static function uploadRoot(): string
    {
        return ARC_ROOT . '/public/uploads';
    }

    /** Izin verilen uzantilar. */
    public static function allowedExtensions(): array
    {
        return array_map('strtolower', (array) Config::get('upload.allowed_ext', array_keys(self::MIME_MAP)));
    }

    /**
     * Yuklenen dosyayi dogrular.
     *
     * @param array $file `$_FILES` girdisi.
     * @return array{ok:bool, error:string, ext:string, mime:string}
     */
    public static function validate(array $file): array
    {
        $fail = static fn (string $message): array => ['ok' => false, 'error' => $message, 'ext' => '', 'mime' => ''];

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            return $fail(match ($error) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Dosya sunucu sinirindan buyuk.',
                UPLOAD_ERR_PARTIAL   => 'Dosya eksik yuklendi.',
                UPLOAD_ERR_NO_FILE   => 'Dosya secilmedi.',
                UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE => 'Sunucuda gecici klasore yazilamadi.',
                default              => 'Dosya yuklenemedi.',
            });
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_file($tmp)) {
            return $fail('Gecici dosya bulunamadi.');
        }

        $maxSize = (int) Config::get('upload.max_size', 5 * 1024 * 1024);
        $size    = (int) ($file['size'] ?? 0);
        if ($size <= 0) {
            return $fail('Dosya bos.');
        }
        if ($size > $maxSize) {
            return $fail('Dosya ' . format_bytes($maxSize) . ' sinirini asiyor.');
        }

        // Uzanti kullanicidan gelen adin SON parcasindan alinir; `resim.php.jpg`
        // gibi adlar burada `jpg` verir, MIME kontrolu asagida yakalar.
        $original = (string) ($file['name'] ?? '');
        $ext      = strtolower((string) pathinfo($original, PATHINFO_EXTENSION));

        if ($ext === '' || !in_array($ext, self::allowedExtensions(), true)) {
            return $fail('Bu dosya turu kabul edilmiyor.');
        }

        // Cift uzantili adlar reddedilir: resim.php.jpg, belge.phtml.png
        $stem = strtolower((string) pathinfo($original, PATHINFO_FILENAME));
        if (preg_match('/\.(php|phtml|phar|php[0-9]|cgi|pl|sh|exe|htaccess|asp|aspx|jsp)$/i', $stem) === 1) {
            return $fail('Cift uzantili dosya adlari kabul edilmiyor.');
        }

        // MIME dogrulamasi; uzantiyla eslesmeli. DOCS.md 10.6, test S-06
        $mime = self::detectMime($tmp);
        if ($mime === null) {
            return $fail('Dosya turu belirlenemedi.');
        }

        if (!in_array($mime, self::MIME_MAP[$ext] ?? [], true)) {
            return $fail('Dosya icerigi uzantisiyla uyusmuyor.');
        }

        // Goruntu dosyalari gercekten goruntu olmali.
        if (in_array($ext, self::RASTER, true)) {
            $info = @getimagesize($tmp);
            if ($info === false) {
                return $fail('Gecerli bir goruntu dosyasi degil.');
            }
        }

        return ['ok' => true, 'error' => '', 'ext' => $ext, 'mime' => $mime];
    }

    /** `finfo` ile MIME tespiti. */
    public static function detectMime(string $path): ?string
    {
        if (!function_exists('finfo_open')) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        return is_string($mime) && $mime !== '' ? $mime : null;
    }

    /**
     * Dosyayi kaydeder, varyantlarini uretir ve `media` kaydini yazar.
     *
     * @return array{ok:bool, error:string, id:int}
     */
    public static function store(array $file, ?int $userId = null, array $alt = []): array
    {
        $check = self::validate($file);
        if (!$check['ok']) {
            return ['ok' => false, 'error' => $check['error'], 'id' => 0];
        }

        $ext      = $check['ext'];
        $relative = date('Y/m');
        $dir      = self::uploadRoot() . '/' . $relative;

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return ['ok' => false, 'error' => 'Yukleme klasoru olusturulamadi.', 'id' => 0];
        }

        // Ad tahmin edilemez; kullanicinin verdigi ad kullanilmaz. DOCS.md 10.6
        $filename = Security::randomFilename($ext);
        $target   = $dir . '/' . $filename;

        if ($ext === 'svg') {
            // SVG once temizlenir, sonra yazilir. DOCS.md 10.6, test S-17
            $raw   = (string) file_get_contents((string) $file['tmp_name']);
            $clean = Security::sanitizeSvg($raw);
            if (@file_put_contents($target, $clean) === false) {
                return ['ok' => false, 'error' => 'Dosya yazilamadi.', 'id' => 0];
            }
        } else {
            $moved = self::moveUploaded((string) $file['tmp_name'], $target);
            if (!$moved) {
                return ['ok' => false, 'error' => 'Dosya tasinamadi.', 'id' => 0];
            }
        }

        @chmod($target, 0644);

        [$width, $height] = self::dimensions($target, $ext);
        $variants         = self::makeVariants($target, $relative, $filename, $ext, $width);

        try {
            $id = Database::instance()->insert('media', [
                'filename' => $filename,
                'path'     => $relative . '/' . $filename,
                'mime'     => $check['mime'],
                'size'     => (int) filesize($target),
                'width'    => $width > 0 ? $width : null,
                'height'   => $height > 0 ? $height : null,
                'variants' => $variants ? Security::json($variants) : null,
                'user_id'  => $userId,
            ]);
        } catch (\Throwable $e) {
            @unlink($target);
            Logger::exception($e);
            return ['ok' => false, 'error' => 'Kayit olusturulamadi.', 'id' => 0];
        }

        foreach ($alt as $lang => $text) {
            self::setAlt($id, (string) $lang, (string) $text);
        }

        return ['ok' => true, 'error' => '', 'id' => $id];
    }

    /** Test edilebilirlik icin ayri; CLI'da `rename` kullanilir. */
    private static function moveUploaded(string $from, string $to): bool
    {
        if (is_uploaded_file($from)) {
            return move_uploaded_file($from, $to);
        }
        return @rename($from, $to) || @copy($from, $to);
    }

    /**
     * Boyut varyantlari ve WebP uretir.
     *
     * DOCS.md 11.4 — gorseller WebP, `width`/`height` yazili.
     * Test F-06: thumb, medium, large, webp uretilir.
     *
     * @return array<string, array{path:string, width:int, height:int}>
     */
    public static function makeVariants(string $path, string $relative, string $filename, string $ext, int $width): array
    {
        if (!in_array($ext, self::RASTER, true) || !extension_loaded('gd')) {
            return [];
        }

        $sizes    = (array) Config::get('upload.variants', ['thumb' => 320, 'medium' => 768, 'large' => 1600]);
        $variants = [];
        $stem     = pathinfo($filename, PATHINFO_FILENAME);
        $dir      = dirname($path);

        $source = self::openImage($path, $ext);
        if ($source === null) {
            return [];
        }

        $sourceWidth  = imagesx($source);
        $sourceHeight = imagesy($source);

        foreach ($sizes as $name => $maxWidth) {
            $maxWidth = (int) $maxWidth;
            if ($maxWidth <= 0) {
                continue;
            }

            // Kaynaktan buyuk varyant uretilmez; buyutme kalite kaybidir.
            $targetWidth  = min($maxWidth, $sourceWidth);
            $targetHeight = (int) max(1, round($sourceHeight * ($targetWidth / $sourceWidth)));

            $variantName = $stem . '-' . $name . '.webp';
            $variantPath = $dir . '/' . $variantName;

            $resized = imagecreatetruecolor($targetWidth, $targetHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

            if (function_exists('imagewebp') && @imagewebp($resized, $variantPath, 82)) {
                @chmod($variantPath, 0644);
                $variants[$name] = [
                    'path'   => $relative . '/' . $variantName,
                    'width'  => $targetWidth,
                    'height' => $targetHeight,
                ];
            }

            imagedestroy($resized);
        }

        // Asil dosyanin WebP karsiligi.
        if (function_exists('imagewebp') && $ext !== 'webp') {
            $webpName = $stem . '.webp';
            $webpPath = $dir . '/' . $webpName;
            if (@imagewebp($source, $webpPath, 86)) {
                @chmod($webpPath, 0644);
                $variants['webp'] = [
                    'path'   => $relative . '/' . $webpName,
                    'width'  => $sourceWidth,
                    'height' => $sourceHeight,
                ];
            }
        }

        imagedestroy($source);

        return $variants;
    }

    private static function openImage(string $path, string $ext)
    {
        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png'         => @imagecreatefrompng($path),
            'gif'         => @imagecreatefromgif($path),
            'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default       => false,
        };

        return $image === false ? null : $image;
    }

    /** @return array{0:int,1:int} */
    public static function dimensions(string $path, string $ext): array
    {
        if ($ext === 'svg') {
            $svg = (string) file_get_contents($path);
            if (preg_match('/viewBox\s*=\s*"[\d.\s-]*?\s([\d.]+)\s+([\d.]+)"/i', $svg, $m) === 1) {
                return [(int) round((float) $m[1]), (int) round((float) $m[2])];
            }
            return [0, 0];
        }

        $info = @getimagesize($path);
        return $info === false ? [0, 0] : [(int) $info[0], (int) $info[1]];
    }

    // --- Kayit erisimi ------------------------------------------------------

    public static function find(int $id): ?array
    {
        $row = Database::instance()->first('SELECT * FROM media WHERE id = :id', [':id' => $id]);
        if ($row === null) {
            return null;
        }

        $row['variants'] = self::decodeVariants($row['variants'] ?? null);
        return $row;
    }

    public static function decodeVariants(mixed $raw): array
    {
        if (!is_string($raw) || $raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Alt metni. Bossa panelde uyari rozeti gosterilir. DOCS.md 9.5 */
    public static function alt(int $id, string $lang): string
    {
        $value = Database::instance()->value(
            'SELECT alt FROM media_translations WHERE media_id = :id AND lang = :lang',
            [':id' => $id, ':lang' => $lang]
        );

        return is_string($value) ? $value : '';
    }

    public static function setAlt(int $id, string $lang, string $alt, string $title = ''): void
    {
        Database::instance()->upsert(
            'media_translations',
            [
                'media_id' => $id,
                'lang'     => $lang,
                'alt'      => mb_substr(trim($alt), 0, 255) ?: null,
                'title'    => mb_substr(trim($title), 0, 255) ?: null,
            ],
            ['alt', 'title']
        );
    }

    /** Dosyanin genel adresi. */
    public static function url(string $path): string
    {
        return '/uploads/' . ltrim($path, '/');
    }

    /**
     * Gorselin nerelerde kullanildigi.
     *
     * Kullanimdaki dosya icin silme uyarisi gosterilir.  DOCS.md 9.5, test F-07
     *
     * @return array<int, array{type:string, label:string, id:int}>
     */
    public static function usage(int $id): array
    {
        $db  = Database::instance();
        $out = [];

        $queries = [
            ['sql' => 'SELECT p.id, t.title FROM pages p
                         JOIN page_translations t ON t.page_id = p.id
                        WHERE p.cover_id = :id GROUP BY p.id, t.title',
             'type' => 'Sayfa kapagi'],
            ['sql' => 'SELECT t.page_id AS id, t.title FROM page_translations t WHERE t.og_image_id = :id',
             'type' => 'Sayfa OG gorseli'],
            ['sql' => 'SELECT id, client_name AS title FROM projects WHERE cover_id = :id',
             'type' => 'Referans kapagi'],
            ['sql' => 'SELECT p.id, p.client_name AS title FROM project_media pm
                         JOIN projects p ON p.id = pm.project_id WHERE pm.media_id = :id',
             'type' => 'Referans galerisi'],
            ['sql' => 'SELECT p.id, t.title FROM posts p
                         JOIN post_translations t ON t.post_id = p.id
                        WHERE p.cover_id = :id GROUP BY p.id, t.title',
             'type' => 'Blog kapagi'],
        ];

        foreach ($queries as $query) {
            try {
                foreach ($db->all($query['sql'], [':id' => $id]) as $row) {
                    $out[] = [
                        'type'  => $query['type'],
                        'label' => (string) ($row['title'] ?? ''),
                        'id'    => (int) ($row['id'] ?? 0),
                    ];
                }
            } catch (\Throwable $e) {
                Logger::exception($e);
            }
        }

        // Icerik govdesinde dogrudan kullanilan gorseller.
        $media = self::find($id);
        if ($media !== null) {
            $needle = '%' . self::url((string) $media['path']) . '%';
            foreach ([
                ['sql' => 'SELECT page_id AS id, title FROM page_translations WHERE content LIKE :needle', 'type' => 'Sayfa icerigi'],
                ['sql' => 'SELECT post_id AS id, title FROM post_translations WHERE content LIKE :needle', 'type' => 'Blog icerigi'],
            ] as $query) {
                try {
                    foreach ($db->all($query['sql'], [':needle' => $needle]) as $row) {
                        $out[] = ['type' => $query['type'], 'label' => (string) $row['title'], 'id' => (int) $row['id']];
                    }
                } catch (\Throwable $e) {
                    Logger::exception($e);
                }
            }
        }

        return $out;
    }

    /** Kaydi ve diskteki tum varyantlari siler. */
    public static function delete(int $id): bool
    {
        $media = self::find($id);
        if ($media === null) {
            return false;
        }

        $root = self::uploadRoot();

        $paths = [(string) $media['path']];
        foreach ($media['variants'] as $variant) {
            if (isset($variant['path'])) {
                $paths[] = (string) $variant['path'];
            }
        }

        foreach ($paths as $path) {
            $full = $root . '/' . ltrim($path, '/');
            // Yol yukleme kokunun disina cikamaz.
            $real = realpath($full);
            if ($real !== false && str_starts_with($real, realpath($root) ?: $root)) {
                @unlink($real);
            }
        }

        Database::instance()->delete('media', ['id' => $id]);

        return true;
    }
}
