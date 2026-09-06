<?php
/**
 * Sunucu tarafi dogrulama.
 *
 * "Her veri dogrulanir, her cikti kacirilir. Istisnasiz."  DOCS.md 1, 10.8
 *
 * Kullanim:
 *   $v = new Validator($request->allPost());
 *   $v->required('name')->max('name', 150)
 *     ->required('email')->email('email');
 *   if ($v->fails()) { ... $v->errors() ... }
 */

declare(strict_types=1);

namespace Arcates\Core;

final class Validator
{
    private array $errors = [];

    public function __construct(private array $data, private array $labels = [])
    {
    }

    public function value(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    private function label(string $field): string
    {
        return $this->labels[$field] ?? $field;
    }

    private function fail(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /** Bu alanda zaten hata varsa sonraki kurallar atlanir. */
    private function clean(string $field): bool
    {
        return !isset($this->errors[$field]);
    }

    private function str(string $field): string
    {
        $value = $this->data[$field] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }

    // --- Kurallar -----------------------------------------------------------

    /** Bos dize, null ve bos dizi reddedilir. Test U-05 */
    public function required(string $field, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;
        $empty = $value === null
            || (is_string($value) && trim($value) === '')
            || (is_array($value) && $value === []);

        if ($empty) {
            $this->fail($field, $message ?? $this->label($field) . ' alani zorunludur.');
        }
        return $this;
    }

    /** Test U-04 */
    public function email(string $field, ?string $message = null): self
    {
        $value = $this->str($field);
        if ($value !== '' && $this->clean($field)) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL) === false || mb_strlen($value) > 190) {
                $this->fail($field, $message ?? 'Gecerli bir e-posta adresi giriniz.');
            }
        }
        return $this;
    }

    /** Turkiye telefon bicimlerini kabul eder. */
    public function phone(string $field, ?string $message = null): self
    {
        $value = $this->str($field);
        if ($value !== '' && $this->clean($field)) {
            $digits = preg_replace('/\D+/', '', $value) ?? '';
            if (strlen($digits) < 10 || strlen($digits) > 15) {
                $this->fail($field, $message ?? 'Gecerli bir telefon numarasi giriniz.');
            }
        }
        return $this;
    }

    public function min(string $field, int $length, ?string $message = null): self
    {
        $value = $this->str($field);
        if ($value !== '' && $this->clean($field) && mb_strlen($value) < $length) {
            $this->fail($field, $message ?? $this->label($field) . " en az {$length} karakter olmalidir.");
        }
        return $this;
    }

    public function max(string $field, int $length, ?string $message = null): self
    {
        $value = $this->str($field);
        if ($value !== '' && $this->clean($field) && mb_strlen($value) > $length) {
            $this->fail($field, $message ?? $this->label($field) . " en fazla {$length} karakter olabilir.");
        }
        return $this;
    }

    public function integer(string $field, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;
        if ($value !== null && $value !== '' && $this->clean($field)) {
            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                $this->fail($field, $message ?? $this->label($field) . ' tam sayi olmalidir.');
            }
        }
        return $this;
    }

    public function between(string $field, int $low, int $high, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;
        if ($value !== null && $value !== '' && $this->clean($field)) {
            $int = (int) $value;
            if ($int < $low || $int > $high) {
                $this->fail($field, $message ?? $this->label($field) . " {$low} ile {$high} arasinda olmalidir.");
            }
        }
        return $this;
    }

    /** Deger verilen listeden biri olmali. */
    public function in(string $field, array $allowed, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;
        if ($value !== null && $value !== '' && $this->clean($field)) {
            if (!in_array((string) $value, array_map('strval', $allowed), true)) {
                $this->fail($field, $message ?? $this->label($field) . ' gecerli bir secim degil.');
            }
        }
        return $this;
    }

    public function url(string $field, ?string $message = null): self
    {
        $value = $this->str($field);
        if ($value !== '' && $this->clean($field)) {
            if (filter_var($value, FILTER_VALIDATE_URL) === false || preg_match('#^https?://#i', $value) !== 1) {
                $this->fail($field, $message ?? 'Gecerli bir adres giriniz (http:// veya https://).');
            }
        }
        return $this;
    }

    /** Slug bicimi: kucuk harf, rakam ve tire. DOCS.md 9.6 */
    public function slug(string $field, ?string $message = null): self
    {
        $value = $this->str($field);
        if ($value !== '' && $this->clean($field) && !Security::isCleanSlug($value)) {
            $this->fail($field, $message ?? 'Adres yalnizca kucuk harf, rakam ve tire icerebilir.');
        }
        return $this;
    }

    /** Sifre en az 10 karakter. DOCS.md 10.4 */
    public function password(string $field, ?string $message = null): self
    {
        $value = (string) ($this->data[$field] ?? '');
        $min   = (int) Config::get('security.password_min', 10);
        if ($this->clean($field) && mb_strlen($value) < $min) {
            $this->fail($field, $message ?? "Sifre en az {$min} karakter olmalidir.");
        }
        return $this;
    }

    public function matches(string $field, string $otherField, ?string $message = null): self
    {
        if ($this->clean($field) && (string) ($this->data[$field] ?? '') !== (string) ($this->data[$otherField] ?? '')) {
            $this->fail($field, $message ?? 'Degerler birbiriyle ayni degil.');
        }
        return $this;
    }

    /** Onay kutusu isaretli olmali. DOCS.md 10.9 */
    public function accepted(string $field, ?string $message = null): self
    {
        $value = $this->data[$field] ?? null;
        if (!in_array($value, ['1', 1, true, 'on', 'evet', 'true'], true)) {
            $this->fail($field, $message ?? 'Bu onayi vermeniz gerekir.');
        }
        return $this;
    }

    /** Ozel kural. */
    public function rule(string $field, callable $check, string $message): self
    {
        if ($this->clean($field) && $check($this->data[$field] ?? null) !== true) {
            $this->fail($field, $message);
        }
        return $this;
    }

    /** Alani dogrudan hatali isaretler. */
    public function addError(string $field, string $message): self
    {
        $this->fail($field, $message);
        return $this;
    }

    // --- Sonuc --------------------------------------------------------------

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /** Alan basina ilk hata mesaji. */
    public function firstErrors(): array
    {
        $out = [];
        foreach ($this->errors as $field => $messages) {
            $out[$field] = $messages[0];
        }
        return $out;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
