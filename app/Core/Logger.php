<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Logger
{
    public function __construct(private Database $db) {}

    public function activity(string $action, ?string $entity = null, ?int $entityId = null, ?string $detail = null, ?int $userId = null, ?string $ip = null): void
    {
        $packedIp = $ip !== null ? @inet_pton($ip) : null;
        $this->db->query(
            'INSERT INTO activity_log (user_id, action, entity, entity_id, detail, ip) VALUES (:user_id, :action, :entity, :entity_id, :detail, :ip)',
            [
                'user_id' => $userId,
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
                'detail' => $detail,
                'ip' => $packedIp === false ? null : $packedIp,
            ]
        );
    }
}
