<?php
/**
 * E-posta gonderimi.
 *
 * Harici kutuphane yoktur; PHP `mail()` kullanilir. `mail.method` degeri
 * `log` ise mesaj `storage/logs/mail.log` dosyasina yazilir — testlerde ve
 * yerel gelistirmede boyle calisir.  DOCS.md 12
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Mailer
{
    /** Testlerde toplanan mesajlar. */
    private static array $sent = [];
    private static bool $capture = false;

    public static function capture(bool $on = true): void
    {
        self::$capture = $on;
        self::$sent    = [];
    }

    public static function sentMessages(): array
    {
        return self::$sent;
    }

    /**
     * Duz metin govdeli e-posta gonderir.
     *
     * @param string $to      Alici adresi.
     * @param string $subject Konu.
     * @param string $body    Duz metin govde.
     */
    public static function send(string $to, string $subject, string $body, array $headersExtra = []): bool
    {
        $to = trim($to);
        if ($to === '' || filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            Logger::warning('Geçersiz e-posta alıcısı', ['to' => $to]);
            return false;
        }

        $fromAddress = (string) Config::get('mail.from', 'site@localhost');
        $fromName    = (string) Config::get('mail.from_name', (string) Config::get('app.name', 'Arcates'));

        // Basliklara satir sonu enjeksiyonu engellenir.
        $subject = self::sanitizeHeader($subject);

        $headers = [
            'MIME-Version'              => '1.0',
            'Content-Type'              => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
            'From'                      => self::sanitizeHeader($fromName) . ' <' . self::sanitizeHeader($fromAddress) . '>',
            'Reply-To'                  => self::sanitizeHeader($fromAddress),
            'X-Mailer'                  => 'Arcates',
        ];

        foreach ($headersExtra as $name => $value) {
            $headers[self::sanitizeHeader((string) $name)] = self::sanitizeHeader((string) $value);
        }

        $message = [
            'to'      => $to,
            'subject' => $subject,
            'body'    => $body,
            'headers' => $headers,
            'at'      => date('c'),
        ];

        if (self::$capture) {
            self::$sent[] = $message;
            return true;
        }

        $method = (string) Config::get('mail.method', 'mail');

        if ($method === 'log' || PHP_SAPI === 'cli') {
            self::toLog($message);
            return true;
        }

        $headerLines = [];
        foreach ($headers as $name => $value) {
            $headerLines[] = $name . ': ' . $value;
        }

        $sent = @mail(
            $to,
            '=?UTF-8?B?' . base64_encode($subject) . '?=',
            $body,
            implode("\r\n", $headerLines),
            '-f' . $fromAddress
        );

        if (!$sent) {
            Logger::error('E-posta gönderilemedi', ['to' => $to, 'subject' => $subject]);
            self::toLog($message);
        }

        return $sent;
    }

    private static function toLog(array $message): void
    {
        $dir = ARC_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $text = "==== " . $message['at'] . " ====\n"
            . 'Kime: ' . $message['to'] . "\n"
            . 'Konu: ' . $message['subject'] . "\n\n"
            . $message['body'] . "\n\n";

        @file_put_contents($dir . '/mail.log', $text, FILE_APPEND | LOCK_EX);
    }

    /** Baslik enjeksiyonunu engeller. */
    private static function sanitizeHeader(string $value): string
    {
        return trim(str_replace(["\r", "\n", "%0a", "%0d"], '', $value));
    }
}
