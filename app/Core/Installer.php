<?php

declare(strict_types=1);

namespace Arcates\Core;

use RuntimeException;

final class Installer
{
    public function __construct(private Database $db, private string $rootPath) {}

    public function isInstalled(): bool
    {
        return is_file($this->lockPath());
    }

    public function install(string $name, string $email, string $password): void
    {
        if ($this->isInstalled()) {
            throw new RuntimeException('Kurulum daha once tamamlanmis.');
        }

        $validator = new Validator();
        if (!$validator->validate(
            ['name' => $name, 'email' => $email, 'password' => $password],
            ['name' => ['required', 'max:120'], 'email' => ['required', 'email', 'max:190'], 'password' => ['required', 'min:10']]
        )) {
            throw new RuntimeException('Kurulum bilgileri gecersiz.');
        }

        $schema = @file_get_contents($this->rootPath . '/db/schema.sql');
        if ($schema === false || trim($schema) === '') {
            throw new RuntimeException('schema.sql okunamadi.');
        }

        $this->db->pdo()->beginTransaction();
        try {
            foreach ($this->statements($schema) as $statement) {
                $this->db->pdo()->exec($statement);
            }
            $this->db->query(
                'INSERT INTO users (name, email, password_hash, role, status) VALUES (:name, :email, :password_hash, :role, 1)',
                [
                    'name' => trim($name),
                    'email' => mb_strtolower(trim($email)),
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'admin',
                ]
            );
            foreach ([['tr','Turkce','ltr',1,0],['en','English','ltr',0,10],['de','Deutsch','ltr',0,20],['ar','العربية','rtl',0,30]] as $language) {
                $this->db->query(
                    'INSERT INTO languages (code, name, direction, is_default, is_active, sort) VALUES (:code, :name, :direction, :is_default, 1, :sort)',
                    ['code'=>$language[0], 'name'=>$language[1], 'direction'=>$language[2], 'is_default'=>$language[3], 'sort'=>$language[4]]
                );
            }
            $sort = 10;
            foreach (['header','hero','strip','services','coast','steps','works','faq','cta','footer'] as $key) {
                $this->db->query(
                    'INSERT INTO home_sections (`key`, is_active, sort, config) VALUES (:key, 1, :sort, NULL)',
                    ['key' => $key, 'sort' => $sort]
                );
                $sort += 10;
            }
            $this->db->pdo()->commit();
        } catch (\Throwable $e) {
            if ($this->db->pdo()->inTransaction()) {
                $this->db->pdo()->rollBack();
            }
            throw $e;
        }

        $storage = $this->rootPath . '/storage';
        if (!is_dir($storage) && !mkdir($storage, 0775, true) && !is_dir($storage)) {
            throw new RuntimeException('storage klasoru olusturulamadi.');
        }
        if (file_put_contents($this->lockPath(), date(DATE_ATOM) . PHP_EOL, LOCK_EX) === false) {
            throw new RuntimeException('Kurulum kilidi yazilamadi.');
        }
    }

    private function statements(string $schema): array
    {
        $schema = preg_replace('/^\s*--.*$/m', '', $schema) ?? $schema;
        return array_values(array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $schema) ?: [])));
    }

    private function lockPath(): string
    {
        return $this->rootPath . '/storage/installed.lock';
    }
}
