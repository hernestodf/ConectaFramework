<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService extends BaseService
{
    private UserRepository $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
        parent::__construct($this->repo);
    }

    public function index(): array
    {
        return $this->repo->all();
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repo->paginate($page, $perPage);
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    public function create(array $data): int
    {
        $this->validate($data);
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $data['password'] = password_hash('123456', PASSWORD_DEFAULT);
        }
        
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): int
    {
        $this->validate($data, $id);
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id) > 0;
    }

    private function validate(array $data, ?int $id = null): void
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Nome é obrigatório';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email é obrigatório';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        } else {
            $existing = $this->repo->findByEmail($data['email']);
            if ($existing && $existing['id'] != $id) {
                $errors[] = 'Email já cadastrado';
            }
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode('. ', $errors));
        }
    }

    public function formatTelefone(?string $telefone): string
    {
        if (empty($telefone)) return '';
        $telefone = preg_replace('/\D/', '', $telefone);
        if (strlen($telefone) == 10) {
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
        }
        return $telefone;
    }

    public function formatCelular(?string $celular): string
    {
        if (empty($celular)) return '';
        $celular = preg_replace('/\D/', '', $celular);
        if (strlen($celular) == 11) {
            return '(' . substr($celular, 0, 2) . ') ' . substr($celular, 2, 5) . '-' . substr($celular, 7);
        }
        return $celular;
    }

    public function formatCep(?string $cep): string
    {
        if (empty($cep)) return '';
        $cep = preg_replace('/\D/', '', $cep);
        if (strlen($cep) == 8) {
            return substr($cep, 0, 5) . '-' . substr($cep, 5);
        }
        return $cep;
    }
}