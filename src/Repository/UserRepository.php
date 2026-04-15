<?php

namespace App\Repository;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'telefone', 'email', 'cep', 'celular', 'password', 'role', 'status'];

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function create(array $data): int
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $data['password'] = password_hash('123456', PASSWORD_DEFAULT);
        }
        
        return parent::create($data);
    }

    public function update(int $id, array $data): int
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }
}