<?php

namespace App\Repository;

class ClienteRepository extends BaseRepository
{
    protected string $table = 'clientes';
    protected array $fillable = [
        'nome', 'cnpj', 'cpf', 'email', 'telefone', 'celular',
        'cep', 'logradouro', 'numero', 'complemento',
        'bairro', 'cidade', 'uf', 'status'
    ];
}