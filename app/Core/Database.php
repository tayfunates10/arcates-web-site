<?php

declare(strict_types=1);

namespace Arcates\Core;

use InvalidArgumentException;
use PDO;
use PDOStatement;

final class Database
{
    private PDO $pdo;

    public function __construct(array|PDO $config)
    {
        $this->pdo = $config instanceof PDO ? $config : new PDO((string) $config['dsn'], (string) ($config['username'] ?? ''), (string) ($config['password'] ?? ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function pdo(): PDO { return $this->pdo; }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public function fetchAll(string $sql, array $params = []): array { return $this->query($sql, $params)->fetchAll(); }
    public function execute(string $sql, array $params = []): int { return $this->query($sql, $params)->rowCount(); }

    public function insert(string $table, array $data): int
    {
        $this->assertIdentifier($table);
        if ($data === []) { throw new InvalidArgumentException('Insert data cannot be empty.'); }
        foreach (array_keys($data) as $column) { $this->assertIdentifier((string) $column); }
        $columns = array_keys($data);
        $quoted = array_map(static fn(string $column): string => '`' . $column . '`', $columns);
        $placeholders = array_map(static fn(string $column): string => ':' . $column, $columns);
        $this->query('INSERT INTO `' . $table . '` (' . implode(',', $quoted) . ') VALUES (' . implode(',', $placeholders) . ')', $data);
        return (int) $this->pdo->lastInsertId();
    }

    private function assertIdentifier(string $identifier): void
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) { throw new InvalidArgumentException('Unsafe SQL identifier.'); }
    }
}
