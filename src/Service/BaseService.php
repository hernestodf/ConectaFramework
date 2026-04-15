<?php

namespace App\Service;

use App\Repository\BaseRepository;
use App\Core\Logger;

abstract class BaseService
{
    protected BaseRepository $repository;
    protected string $entityName = 'Entity';

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        $message = "{$this->entityName}: {$message}";
        Logger::$level($message, $context);
    }

    protected function findOrFail(int $id): ?array
    {
        $data = $this->repository->find($id);
        
        if (!$data) {
            throw new \Exception("{$this->entityName} não encontrado(a): {$id}");
        }
        
        return $data;
    }

    protected function validateRequired(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \Exception("Campo obrigatório: {$field}");
            }
        }
    }

    protected function sanitize(array $data): array
    {
        return array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginate($page, $perPage);
    }
}