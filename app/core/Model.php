<?php
/**
 * Base Model - Tüm modeller buradan türer
 */
class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';

    protected function db(): PDO
    {
        return Database::getInstance();
    }

    /** Tümünü getir */
    public function all(string $orderBy = 'id DESC'): array
    {
        return Database::query("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
    }

    /** ID ile tek kayıt getir */
    public function find(int $id): array|false
    {
        return Database::queryOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Koşullu tek kayıt getir */
    public function findBy(string $field, mixed $value): array|false
    {
        return Database::queryOne(
            "SELECT * FROM `{$this->table}` WHERE `{$field}` = ? LIMIT 1",
            [$value]
        );
    }

    /** Koşullu çoklu kayıt getir */
    public function where(string $field, mixed $value, string $orderBy = 'id DESC'): array
    {
        return Database::query(
            "SELECT * FROM `{$this->table}` WHERE `{$field}` = ? ORDER BY {$orderBy}",
            [$value]
        );
    }

    /** Kayıt oluştur */
    public function create(array $data): int|false
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        Database::execute($sql, array_values($data));
        return (int) Database::lastInsertId();
    }

    /** Güncelle */
    public function update(int $id, array $data): int
    {
        $set = implode(' = ?, ', array_map(fn($col) => "`{$col}`", array_keys($data))) . ' = ?';
        $sql = "UPDATE `{$this->table}` SET {$set} WHERE `{$this->primaryKey}` = ?";
        return Database::execute($sql, [...array_values($data), $id]);
    }

    /** Sil */
    public function delete(int $id): int
    {
        return Database::execute(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /** Sayfa başına kayıt */
    public function paginate(int $page, int $perPage = PER_PAGE, string $where = '', array $params = [], string $orderBy = 'id DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $whereClause = $where ? "WHERE {$where}" : '';

        $total = Database::queryOne(
            "SELECT COUNT(*) as cnt FROM `{$this->table}` {$whereClause}",
            $params
        )['cnt'];

        $rows = Database::query(
            "SELECT * FROM `{$this->table}` {$whereClause} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'        => $rows,
            'total'       => (int) $total,
            'per_page'    => $perPage,
            'current_page'=> $page,
            'last_page'   => (int) ceil($total / $perPage),
        ];
    }

    /** Toplam kayıt sayısı */
    public function count(string $where = '', array $params = []): int
    {
        $whereClause = $where ? "WHERE {$where}" : '';
        $result = Database::queryOne(
            "SELECT COUNT(*) as cnt FROM `{$this->table}` {$whereClause}",
            $params
        );
        return (int) ($result['cnt'] ?? 0);
    }
}
