<?php
/**
 * HTTP yaniti.
 *
 * Govde, durum kodu ve basliklar burada toplanir; `send()` cagrilmadan hicbir
 * cikti uretilmez. Boylece yonlendirme ve hata yanitlari test edilebilir.
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Response
{
    private array $headers = [];

    public function __construct(
        private string $body = '',
        private int $status = 200,
        array $headers = []
    ) {
        foreach ($headers as $name => $value) {
            $this->header((string) $name, (string) $value);
        }
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public static function text(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public static function xml(string $body, int $status = 200): self
    {
        return new self($body, $status, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self(
            Security::json($data),
            $status,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    /** CSV indirmesi. DOCS.md 9.9, 9.10 */
    public static function csv(string $body, string $filename): self
    {
        return new self("\xEF\xBB\xBF" . $body, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '', $filename) . '"',
        ]);
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function headerLine(string $name): ?string
    {
        foreach ($this->headers as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }
        return null;
    }

    /** Yaniti istemciye gonderir. */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }
        echo $this->body;
    }
}
