<?php

namespace App\Repository;

use App\Database\Connection;
use PDO;

abstract class BaseRepository
{
    protected string $table;
    protected array $fillable = [];

    public function all(): array
    {
        return Connection::query("SELECT * FROM {$this->table}");
    }

    public function find(int $id): ?array
    {
        $results = Connection::query(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
        return $results[0] ?? null;
    }

    public function findBy(string $field, $value): ?array
    {
        $results = Connection::query(
            "SELECT * FROM {$this->table} WHERE $field = ?",
            [$value]
        );
        return $results[0] ?? null;
    }

    public function create(array $data): int
    {
        $data = $this->fill($data);
        
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        Connection::exec(
            "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)",
            array_values($data)
        );
        
        return (int) Connection::lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $data = $this->fill($data);
        
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';
        
        return Connection::exec(
            "UPDATE {$this->table} SET $sets WHERE id = ?",
            array_merge(array_values($data), [$id])
        );
    }

    public function delete(int $id): int
    {
        return Connection::exec(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        
        $results = Connection::query(
            "SELECT * FROM {$this->table} LIMIT $perPage OFFSET $offset"
        );
        
        $total = Connection::query("SELECT COUNT(*) as total FROM {$this->table}");
        
        return [
            'data' => $results,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total[0]['total'] ?? 0,
                'last_page' => ceil(($total[0]['total'] ?? 1) / $perPage),
            ]
        ];
    }

    protected function fill(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    public function __call(string $method, array $args)
    {
        if (str_starts_with($method, 'findBy')) {
            $field = lcfirst(substr($method, 6));
            return $this->findBy($field, $args[0]);
        }
        
        throw new \BadMethodCallException("Método $method não existe");
    }
}