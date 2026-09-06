<?php

declare(strict_types=1);

namespace Arcates\Core;

final class LoginThrottle
{
    public const MAX_FAILURES = 5;
    public const WINDOW_MINUTES = 15;

    public static function locked(int $ipFailures, int $emailFailures): bool
    {
        return $ipFailures >= self::MAX_FAILURES || $emailFailures >= self::MAX_FAILURES;
    }
}
