<?php

namespace App\Service;

use App\Repository\ClienteRepository;

class ClienteService extends BaseService
{
    private ClienteRepository $repo;

    public function __construct()
    {
        $this->repo = new ClienteRepository();
        parent::__construct($this->repo);
    }

    public function create(array $data): int
    {
        $this->validate($data);
        $data = $this->sanitizeCNPJ($data);
        return $this->repo->create(parent::sanitize($data));
    }

    public function update(int $id, array $data): int
    {
        $this->findOrFail($id);
        $data = $this->sanitizeCNPJ($data);
        return $this->repo->update($id, parent::sanitize($data));
    }

    public function delete(int $id): int
    {
        $this->findOrFail($id);
        return $this->repo->delete($id);
    }

    public function activate(int $id): int
    {
        $this->findOrFail($id);
        return $this->repo->update($id, ['status' => 1]);
    }

    public function deactivate(int $id): int
    {
        $this->findOrFail($id);
        return $this->repo->update($id, ['status' => 0]);
    }

    private function validate(array $data, ?int $id = null): void
    {
        $errors = [];

        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }

        if (!empty($data['cnpj']) && strlen(preg_replace('/\D/', '', $data['cnpj'])) !== 14) {
            $errors[] = 'CNPJ inválido';
        }

        if (!empty($data['cpf']) && strlen(preg_replace('/\D/', '', $data['cpf'])) !== 11) {
            $errors[] = 'CPF inválido';
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode('. ', $errors));
        }
    }

    private function sanitizeCNPJ(array $data): array
    {
        if (!empty($data['cnpj'])) {
            $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);
        }
        if (!empty($data['cpf'])) {
            $data['cpf'] = preg_replace('/\D/', '', $data['cpf']);
        }
        if (!empty($data['cep'])) {
            $data['cep'] = preg_replace('/\D/', '', $data['cep']);
        }
        return $data;
    }
}