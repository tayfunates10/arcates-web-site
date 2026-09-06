<?php
/**
 * Dosya gunlugu ve islem gunlugu (activity_log).
 *
 * DOCS.md 9.11, 10.5, 10.7
 */

declare(strict_types=1);

namespace Arcates\Core;

use Throwable;

final class Logger
{
    /** Uygulama gunlugune satir yazar. */
    public static function write(string $level, string $message, array $context = []): void
    {
        $dir = ARC_ROOT . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $line = sprintf(
            "[%s] %s: %s%s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context ? ' ' . Security::json($context) : ''
        );

        @file_put_contents($dir . '/app-' . date('Y-m') . '.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('warning', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }

    public static function exception(Throwable $e): void
    {
        self::write('error', get_class($e) . ': ' . $e->getMessage(), [
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())[0] ?? '',
        ]);
    }

    /**
     * Panel islemlerini veritabanina yazar. DOCS.md 9.11
     *
     * Gunluk yazimi asil isi engellememelidir; hata halinde sessizce dosyaya
     * duser.
     */
    public static function activity(
        string $action,
        ?string $entity = null,
        ?int $entityId = null,
        ?string $detail = null,
        ?int $userId = null,
        ?string $ip = null
    ): void {
        try {
            Database::instance()->insert('activity_log', [
                'user_id'   => $userId ?? Auth::id(),
                'action'    => substr($action, 0, 60),
                'entity'    => $entity !== null ? substr($entity, 0, 60) : null,
                'entity_id' => $entityId,
                'detail'    => $detail !== null ? substr($detail, 0, 2000) : null,
                'ip'        => Security::packIp($ip ?? ($_SERVER['REMOTE_ADDR'] ?? null)),
            ]);
        } catch (Throwable $e) {
            self::write('warning', 'Islem gunlugu yazilamadi: ' . $e->getMessage(), [
                'action' => $action,
                'entity' => $entity,
            ]);
        }
    }
}
