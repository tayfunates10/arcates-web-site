<?php
/**
 * Guvenlik yardimcilari: kacirma, slug uretimi, CSRF, zengin metin ve SVG
 * temizligi, guvenlik basliklari.
 *
 * DOCS.md 10.2, 10.5, 10.6, 11.4
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Security
{
    /** Zengin metinde izin verilen etiketler. DOCS.md 10.2 */
    public const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li',
        'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'a', 'img',
        'figure', 'figcaption', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'hr', 'span', 'div', 'code', 'pre', 'small', 'sup', 'sub',
    ];

    /** Etiket bazinda izin verilen nitelikler. */
    public const ALLOWED_ATTRS = [
        'a'    => ['href', 'title', 'target', 'rel'],
        'img'  => ['src', 'alt', 'title', 'width', 'height', 'loading', 'decoding'],
        'td'   => ['colspan', 'rowspan'],
        'th'   => ['colspan', 'rowspan', 'scope'],
        'span' => ['class'],
        'div'  => ['class'],
        'p'    => ['class'],
        'code' => ['class'],
    ];

    /** Turkce ve yaygin latin harflerin ASCII karsiligi. DOCS.md 11.4 */
    private const TRANSLITERATION = [
        'ç' => 'c', 'Ç' => 'c',
        'ğ' => 'g', 'Ğ' => 'g',
        'ı' => 'i', 'I' => 'i', 'İ' => 'i', 'i' => 'i',
        'ö' => 'o', 'Ö' => 'o',
        'ş' => 's', 'Ş' => 's',
        'ü' => 'u', 'Ü' => 'u',
        'â' => 'a', 'Â' => 'a', 'î' => 'i', 'Î' => 'i', 'û' => 'u', 'Û' => 'u',
        'ä' => 'a', 'Ä' => 'a', 'ß' => 'ss',
        'é' => 'e', 'É' => 'e', 'è' => 'e', 'È' => 'e', 'ê' => 'e', 'Ê' => 'e',
        'á' => 'a', 'Á' => 'a', 'à' => 'a', 'À' => 'a',
        'ó' => 'o', 'Ó' => 'o', 'ò' => 'o', 'Ò' => 'o', 'ô' => 'o', 'Ô' => 'o',
        'ú' => 'u', 'Ú' => 'u', 'ù' => 'u', 'Ù' => 'u',
        'ñ' => 'n', 'Ñ' => 'n', 'ç' => 'c',
    ];

    // --- Cikti kacirma ------------------------------------------------------

    /**
     * HTML kacirma. Ekrana basilan her deger bu fonksiyondan gecer.
     * DOCS.md 10.2
     */
    public static function e(mixed $value): string
    {
        if ($value === null || is_bool($value)) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /** HTML niteligi icinde kullanilacak deger. */
    public static function attr(mixed $value): string
    {
        return self::e($value);
    }

    /**
     * Guvenli JSON. DOCS.md 10.2
     */
    public static function json(mixed $value, int $extraFlags = 0): string
    {
        $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | $extraFlags;

        $out = json_encode($value, $flags);
        return $out === false ? 'null' : $out;
    }

    /** URL icin kacirma; yalnizca guvenli semalara izin verir. */
    public static function url(string $url): string
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return '';
        }
        if (preg_match('#^(https?:|mailto:|tel:|/|\#|\?)#i', $trimmed) !== 1) {
            return '';
        }
        return self::e($trimmed);
    }

    // --- Slug ---------------------------------------------------------------

    /**
     * URL parcasi uretir. Turkce karakterler ASCII'ye cevrilir.
     * DOCS.md 11.4 — c→c, g→g, i→i, I→i, o→o, s→s, u→u
     */
    public static function slug(string $text): string
    {
        $text = strtr($text, self::TRANSLITERATION);
        $text = mb_strtolower($text, 'UTF-8');

        // Ayrilmis birlestirici isaretleri dusur.
        $text = preg_replace('/\p{Mn}+/u', '', $text) ?? $text;

        // Harf ve rakam disindaki her sey ayirici olur.
        $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text) ?? $text;
        $text = trim($text, '-');
        $text = preg_replace('/-{2,}/', '-', $text) ?? $text;

        return $text;
    }

    /** Slug gecerli mi? Turkce karakter ve bosluk icermemeli. DOCS.md 9.6 */
    public static function isCleanSlug(string $slug): bool
    {
        return $slug !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1;
    }

    // --- CSRF ---------------------------------------------------------------

    /** Oturumdaki CSRF belirtecini dondurur, yoksa uretir. DOCS.md 10.5 */
    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return (string) $_SESSION['_csrf'];
    }

    /** Gizli girdi alani olarak CSRF belirteci. */
    public static function csrfField(): string
    {
        return '<input type="hidden" name="_token" value="' . self::e(self::csrfToken()) . '">';
    }

    /** Gelen belirteci sabit zamanli karsilastirir. DOCS.md 10.5 */
    public static function csrfCheck(?string $token): bool
    {
        $expected = $_SESSION['_csrf'] ?? '';
        if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($expected, $token);
    }

    // --- Zengin metin -------------------------------------------------------

    /**
     * Yalnizca yoneticinin girdigi zengin metni temizler.
     * Izin verilmeyen etiket ve nitelikler dusurulur. DOCS.md 10.2
     */
    public static function sanitizeHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        // Once tehlikeli bloklari tamamen kaldir.
        $html = preg_replace('#<\s*(script|style|iframe|object|embed|form|link|meta)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;
        $html = preg_replace('#<\s*(script|style|iframe|object|embed|form|link|meta)\b[^>]*/?>#i', '', $html) ?? $html;

        $allowed = '<' . implode('><', self::ALLOWED_TAGS) . '>';
        $html    = strip_tags($html, $allowed);

        // Kalan etiketlerin niteliklerini beyaz listeye gore ayikla.
        $html = preg_replace_callback(
            '#<\s*([a-z0-9]+)([^>]*)>#i',
            static function (array $m): string {
                $tag     = strtolower($m[1]);
                $allowed = self::ALLOWED_ATTRS[$tag] ?? [];
                if (!$allowed) {
                    return '<' . $tag . '>';
                }

                $kept = [];
                if (preg_match_all('#([a-zA-Z0-9:_-]+)\s*=\s*("([^"]*)"|\'([^\']*)\')#', $m[2], $attrs, PREG_SET_ORDER)) {
                    foreach ($attrs as $a) {
                        $name  = strtolower($a[1]);
                        $value = $a[3] !== '' ? $a[3] : ($a[4] ?? '');
                        if (!in_array($name, $allowed, true)) {
                            continue;
                        }
                        if (in_array($name, ['href', 'src'], true)) {
                            if (preg_match('#^\s*(javascript|data|vbscript)\s*:#i', $value) === 1) {
                                continue;
                            }
                        }
                        $kept[] = $name . '="' . self::e($value) . '"';
                    }
                }

                return '<' . $tag . ($kept ? ' ' . implode(' ', $kept) : '') . '>';
            },
            $html
        ) ?? $html;

        return $html;
    }

    /** Duz metne indirger; kelime sayimi ve benzerlik olcumu icin. */
    public static function toPlainText(string $html): string
    {
        $text = preg_replace('#<\s*(script|style)\b[^>]*>.*?<\s*/\s*\1\s*>#is', ' ', $html) ?? $html;

        // Etiketler bosluga cevrilir; aksi halde `</h2><p>` sinirindaki iki
        // kelime birlesir ve kelime sayimi eksik cikar. DOCS.md 9.6
        $text = preg_replace('#<[^>]*>#s', ' ', $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }

    /** Kelime sayar. DOCS.md 9.6 */
    public static function wordCount(string $html): int
    {
        $text = self::toPlainText($html);
        if ($text === '') {
            return 0;
        }
        $words = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $words ? count($words) : 0;
    }

    // --- SVG ----------------------------------------------------------------

    /**
     * Yuklenen SVG'den script, on* nitelikleri ve xlink:href temizlenir.
     * DOCS.md 10.6, test S-17
     */
    public static function sanitizeSvg(string $svg): string
    {
        // <script> ve <foreignObject> bloklari
        $svg = preg_replace('#<\s*script\b[^>]*>.*?<\s*/\s*script\s*>#is', '', $svg) ?? $svg;
        $svg = preg_replace('#<\s*script\b[^>]*/?>#i', '', $svg) ?? $svg;
        $svg = preg_replace('#<\s*foreignObject\b[^>]*>.*?<\s*/\s*foreignObject\s*>#is', '', $svg) ?? $svg;

        // Olay nitelikleri: onload, onclick, on...
        $svg = preg_replace('#\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $svg) ?? $svg;

        // xlink:href ve href — harici kaynak yuklemesini engeller
        $svg = preg_replace('#\s(xlink:href|href)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $svg) ?? $svg;

        // javascript: semasi
        $svg = preg_replace('#javascript\s*:#i', '', $svg) ?? $svg;

        // <use>, <a>, <set>, <animate> gibi yonlendirme tasiyicilari
        $svg = preg_replace('#<\s*/?\s*(use|a|set|animate|handler)\b[^>]*>#i', '', $svg) ?? $svg;

        // DOCTYPE ve harici varlik bildirimleri (XXE)
        $svg = preg_replace('#<!DOCTYPE[^>]*>#i', '', $svg) ?? $svg;
        $svg = preg_replace('#<!ENTITY[^>]*>#i', '', $svg) ?? $svg;

        return trim($svg);
    }

    // --- Basliklar ----------------------------------------------------------

    /**
     * Guvenlik basliklarini gonderir. DOCS.md 10.7
     *
     * @param bool $admin Panelde satir ici script yasaktir; CSP daha sikidir.
     */
    public static function sendHeaders(bool $admin = false): void
    {
        if (headers_sent()) {
            return;
        }

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-Permitted-Cross-Domain-Policies: none');
        header_remove('X-Powered-By');

        header('Content-Security-Policy: ' . self::csp($admin));
    }

    /** Icerik guvenlik politikasi dizesi. DOCS.md 10.7 */
    public static function csp(bool $admin = false): string
    {
        // On yuzde yalnizca <head> icindeki tek satir ici script vardir
        // (html.js sinifi). Bu yuzden ondan bir karma uretilir; panelde
        // satir ici script hic bulunmaz.
        $scriptSrc = $admin
            ? "'self'"
            : "'self' 'unsafe-hashes' 'sha256-" . self::jsFlagHash() . "'";

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "img-src 'self' data:",
            "style-src 'self' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "connect-src 'self'",
            'script-src ' . $scriptSrc,
        ]);
    }

    /** `html.js` bayragini ekleyen satir ici scriptin govdesi. DOCS.md 7.1 */
    public const JS_FLAG_SCRIPT = "document.documentElement.className+=' js';";

    /** Satir ici script icin CSP karmasi. */
    public static function jsFlagHash(): string
    {
        return base64_encode(hash('sha256', self::JS_FLAG_SCRIPT, true));
    }

    // --- Cesitli ------------------------------------------------------------

    /** Yuklenen dosya icin tahmin edilemez ad. DOCS.md 10.6 */
    public static function randomFilename(string $extension): string
    {
        $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', $extension) ?? '');
        return bin2hex(random_bytes(8)) . ($extension !== '' ? '.' . $extension : '');
    }

    /** IP adresini ikili bicime cevirir (VARBINARY(16) sutunu icin). */
    public static function packIp(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }
        $packed = @inet_pton($ip);
        return $packed === false ? null : $packed;
    }

    /** Ikili IP'yi okunur bicime cevirir. */
    public static function unpackIp(?string $binary): ?string
    {
        if ($binary === null || $binary === '') {
            return null;
        }
        $ip = @inet_ntop($binary);
        return $ip === false ? null : $ip;
    }

    /** Ziyaretci oturumunu kimliklendirmeden ayirt eden karma. DOCS.md 8.4 */
    public static function sessionHash(string $ip, string $userAgent, string $salt = ''): string
    {
        return hash('sha256', $ip . '|' . $userAgent . '|' . date('Y-m-d') . '|' . $salt);
    }
}
