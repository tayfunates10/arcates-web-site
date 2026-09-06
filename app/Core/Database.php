<?php
/**
 * PDO sarmalayicisi.
 *
 * Tum sorgular hazirlanmis ifadedir. SQL icine degisken birlestirilmez.
 * Tablo veya sutun adi degiskenden gelecekse `identifier()` beyaz listesinden
 * gecer.  DOCS.md 10.1
 */

declare(strict_types=1);

namespace Arcates\Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

final class Database
{
    private static ?self $instance = null;

    private ?PDO $pdo = null;
    private int $queryCount = 0;
    private array $queryLog = [];

    private function __construct(private array $config)
    {
    }

    /** Tekil ornek. */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self([
                'host'    => (string) Config::get('db.host', 'localhost'),
                'port'    => (int) Config::get('db.port', 3306),
                'name'    => (string) Config::get('db.name', ''),
                'user'    => (string) Config::get('db.user', ''),
                'pass'    => (string) Config::get('db.pass', ''),
                'charset' => (string) Config::get('db.charset', 'utf8mb4'),
            ]);
        }
        return self::$instance;
    }

    /** Testlerde baglantiyi degistirmek icin. */
    public static function swap(?self $db): void
    {
        self::$instance = $db;
    }

    /** Hazir bir PDO nesnesinden ornek uretir (testler ve kurulum icin). */
    public static function fromPdo(PDO $pdo): self
    {
        $db      = new self([]);
        $db->pdo = $pdo;
        return $db;
    }

    /** Baglanti kurar. Basarisizsa RuntimeException firlatir. */
    public function connect(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $this->config['host'] ?? 'localhost',
            (int) ($this->config['port'] ?? 3306),
            $this->config['name'] ?? '',
            $this->config['charset'] ?? 'utf8mb4'
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                (string) ($this->config['user'] ?? ''),
                (string) ($this->config['pass'] ?? ''),
                [
                    // DOCS.md 10.1 — uc ayar da zorunludur.
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_STRINGIFY_FETCHES  => false,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Veritabani baglantisi kurulamadi: ' . $e->getMessage(), 0, $e);
        }

        return $this->pdo;
    }

    /** Baglanti kurulabiliyor mu? Kurulum ekrani ve testler icin. */
    public function canConnect(): bool
    {
        try {
            $this->connect();
            return true;
        } catch (RuntimeException) {
            return false;
        }
    }

    public function pdo(): PDO
    {
        return $this->connect();
    }

    /** Hazirlanmis ifadeyi calistirir. */
    public function run(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->connect()->prepare($sql);

        foreach ($params as $key => $value) {
            $name = is_int($key) ? $key + 1 : $key;
            $type = match (true) {
                is_int($value)  => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                $value === null => PDO::PARAM_NULL,
                default         => PDO::PARAM_STR,
            };
            $statement->bindValue($name, $value, $type);
        }

        $statement->execute();

        $this->queryCount++;
        if (count($this->queryLog) < 200) {
            $this->queryLog[] = $sql;
        }

        return $statement;
    }

    /** Tum satirlari dondurur. */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /** Ilk satiri dondurur, yoksa null. */
    public function first(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Ilk satirin ilk sutununu dondurur. */
    public function value(string $sql, array $params = []): mixed
    {
        $row = $this->run($sql, $params)->fetch(PDO::FETCH_NUM);
        return $row === false ? null : $row[0];
    }

    /** Tek sutunu duz dizi olarak dondurur. */
    public function column(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Satir ekler ve yeni kimligi dondurur.
     * Sutun adlari beyaz listeden gecer, degerler baglanir.  DOCS.md 10.1
     */
    public function insert(string $table, array $data): int
    {
        $table   = self::identifier($table);
        $columns = [];
        $holders = [];
        $params  = [];

        foreach ($data as $column => $value) {
            $safe            = self::identifier((string) $column);
            $columns[]       = '`' . $safe . '`';
            $holders[]       = ':' . $safe;
            $params[':' . $safe] = $value;
        }

        $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $holders) . ')';
        $this->run($sql, $params);

        return (int) $this->connect()->lastInsertId();
    }

    /** Varsa gunceller yoksa ekler. */
    public function upsert(string $table, array $data, array $updateColumns): void
    {
        $table   = self::identifier($table);
        $columns = [];
        $holders = [];
        $params  = [];

        foreach ($data as $column => $value) {
            $safe            = self::identifier((string) $column);
            $columns[]       = '`' . $safe . '`';
            $holders[]       = ':' . $safe;
            $params[':' . $safe] = $value;
        }

        $updates = [];
        foreach ($updateColumns as $column) {
            $safe      = self::identifier((string) $column);
            $updates[] = '`' . $safe . '` = VALUES(`' . $safe . '`)';
        }

        $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $holders) . ')'
            . ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);

        $this->run($sql, $params);
    }

    /**
     * Satir gunceller ve etkilenen satir sayisini dondurur.
     *
     * @param array $where Sutun => deger esitlik kosullari.
     */
    public function update(string $table, array $data, array $where): int
    {
        $table  = self::identifier($table);
        $set    = [];
        $params = [];

        foreach ($data as $column => $value) {
            $safe                    = self::identifier((string) $column);
            $set[]                   = '`' . $safe . '` = :set_' . $safe;
            $params[':set_' . $safe] = $value;
        }

        [$clause, $whereParams] = self::whereClause($where);
        $params                 = array_merge($params, $whereParams);

        $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $set) . ' WHERE ' . $clause;

        return $this->run($sql, $params)->rowCount();
    }

    /** Satir siler ve etkilenen satir sayisini dondurur. */
    public function delete(string $table, array $where): int
    {
        $table                  = self::identifier($table);
        [$clause, $whereParams] = self::whereClause($where);

        return $this->run('DELETE FROM `' . $table . '` WHERE ' . $clause, $whereParams)->rowCount();
    }

    /** Kosula uyan satirlari sayar. */
    public function count(string $table, array $where = []): int
    {
        $table = self::identifier($table);
        if (!$where) {
            return (int) $this->value('SELECT COUNT(*) FROM `' . $table . '`');
        }
        [$clause, $params] = self::whereClause($where);
        return (int) $this->value('SELECT COUNT(*) FROM `' . $table . '` WHERE ' . $clause, $params);
    }

    /** Islem baslatir, geri sarar veya onaylar. */
    public function transaction(callable $fn): mixed
    {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            $result = $fn($this);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /** Tablo var mi? */
    public function tableExists(string $table): bool
    {
        try {
            $this->run('SELECT 1 FROM `' . self::identifier($table) . '` LIMIT 1');
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /** Bu istekte calisan sorgu sayisi. Test P-05 icin. */
    public function queryCount(): int
    {
        return $this->queryCount;
    }

    public function queryLog(): array
    {
        return $this->queryLog;
    }

    /**
     * Tablo veya sutun adini dogrular.
     * Yalnizca harf, rakam ve alt cizgi kabul edilir; baska hicbir sey SQL'e
     * girmez.  DOCS.md 10.1
     */
    public static function identifier(string $name): string
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]{0,63}$/', $name) !== 1) {
            throw new RuntimeException('Gecersiz tablo veya sutun adi: ' . $name);
        }
        return $name;
    }

    /**
     * @return array{0:string,1:array}
     */
    private static function whereClause(array $where): array
    {
        if (!$where) {
            throw new RuntimeException('Kosulsuz guncelleme veya silme yasaktir.');
        }

        $parts  = [];
        $params = [];
        foreach ($where as $column => $value) {
            $safe = self::identifier((string) $column);
            if ($value === null) {
                $parts[] = '`' . $safe . '` IS NULL';
                continue;
            }
            $parts[]                  = '`' . $safe . '` = :where_' . $safe;
            $params[':where_' . $safe] = $value;
        }

        return [implode(' AND ', $parts), $params];
    }
}
