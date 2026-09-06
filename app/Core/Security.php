<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Security
{
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function slug(string $value): string
    {
        $value = strtr($value, [
            'Ç'=>'C','Ğ'=>'G','İ'=>'I','I'=>'I','Ö'=>'O','Ş'=>'S','Ü'=>'U',
            'ç'=>'c','ğ'=>'g','ı'=>'i','i'=>'i','ö'=>'o','ş'=>'s','ü'=>'u',
        ]);
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_token']) || !is_string($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }

    public static function validCsrf(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION['_token'])
            && is_string($_SESSION['_token'])
            && hash_equals($_SESSION['_token'], $token);
    }

    public static function json(mixed $value): string
    {
        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    }
}
