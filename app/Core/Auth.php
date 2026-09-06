<?php

declare(strict_types=1);

namespace Arcates\Core;

final class Auth
{
    public function __construct(private Database $db, private Logger $logger) {}

    public function login(string $email, string $password, string $ip): bool
    {
        $email = mb_strtolower(trim($email));
        $packedIp = @inet_pton($ip);
        if ($packedIp === false) {
            $packedIp = inet_pton('0.0.0.0');
        }

        if ($this->isLocked($email, $packedIp)) {
            $this->logger->activity('login_locked', 'user', null, null, null, $ip);
            return false;
        }

        $user = $this->db->fetch(
            'SELECT id, name, email, password_hash, role, status FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        );
        $valid = $user !== null
            && (int) $user['status'] === 1
            && password_verify($password, (string) $user['password_hash']);

        $this->recordAttempt($email, $packedIp, $valid);
        if (!$valid) {
            $this->logger->activity('login_failed', 'user', null, null, null, $ip);
            return false;
        }

        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Session::set('user_role', (string) $user['role']);
        Session::set('user_name', (string) $user['name']);
        $this->db->query('UPDATE users SET last_login_at = NOW() WHERE id = :id', ['id' => (int) $user['id']]);
        $this->logger->activity('login_success', 'user', (int) $user['id'], null, (int) $user['id'], $ip);
        return true;
    }

    public function logout(string $ip): void
    {
        $id = $this->id();
        if ($id !== null) {
            $this->logger->activity('logout', 'user', $id, null, $id, $ip);
        }
        Session::destroy();
    }

    public function check(): bool { return $this->id() !== null; }
    public function id(): ?int { $id = Session::get('user_id'); return $id === null ? null : (int) $id; }
    public function role(): ?string { $role = Session::get('user_role'); return is_string($role) ? $role : null; }
    public function can(string $requiredRole): bool { $role = $this->role(); return $requiredRole === 'editor' ? in_array($role, ['admin','editor'], true) : $role === 'admin'; }

    private function isLocked(string $email, string $packedIp): bool
    {
        $byIp = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM login_attempts WHERE ip = :ip AND success = 0 AND attempted_at >= (NOW() - INTERVAL 15 MINUTE)',
            ['ip' => $packedIp]
        );
        $byEmail = $this->db->fetch(
            'SELECT COUNT(*) AS total FROM login_attempts WHERE email = :email AND success = 0 AND attempted_at >= (NOW() - INTERVAL 15 MINUTE)',
            ['email' => $email]
        );
        return LoginThrottle::locked((int) ($byIp['total'] ?? 0), (int) ($byEmail['total'] ?? 0));
    }

    private function recordAttempt(string $email, string $packedIp, bool $success): void
    {
        $this->db->query(
            'INSERT INTO login_attempts (ip, email, success) VALUES (:ip, :email, :success)',
            ['ip' => $packedIp, 'email' => $email, 'success' => $success ? 1 : 0]
        );
    }
}
