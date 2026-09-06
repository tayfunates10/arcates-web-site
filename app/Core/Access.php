<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Access
{
    public const ALLOW = 200;
    public const REDIRECT_LOGIN = 302;
    public const FORBIDDEN = 403;

    public static function decide(?int $userId, ?string $role, string $requiredRole = 'editor'): int
    {
        if ($userId === null) {
            return self::REDIRECT_LOGIN;
        }
        if ($requiredRole === 'admin' && $role !== 'admin') {
            return self::FORBIDDEN;
        }
        if (!in_array($role, ['admin', 'editor'], true)) {
            return self::FORBIDDEN;
        }
        return self::ALLOW;
    }
}
