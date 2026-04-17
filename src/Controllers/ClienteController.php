<?php

namespace App\Controllers;

use App\Http\Controller;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Env;
use App\Service\ClienteService;

class ClienteController extends Controller
{
    private ClienteService $service;
    private string $baseUrl;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->service = new ClienteService();
        $this->baseUrl = rtrim(Env::get('BASE_URL', ''), '/');
    }

    public function index(): Response
    {
        $page = (int) $this->get('page', 1);
        $data = $this->service->paginate($page, 15);

        return $this->view('cliente/index', [
            'title' => 'Clientes',
            'clientes' => $data['data'],
            'pagination' => $data['pagination'],
        ]);
    }

    public function create(): Response
    {
        return $this->view('cliente/create', [
            'title' => 'Novo Cliente',
        ]);
    }

    public function store(): Response
    {
        $csrfToken = $this->post('_csrf_token');
        
        if (!Csrf::validate($csrfToken)) {
            return $this->view('cliente/create', [
                'title' => 'Novo Cliente',
                'error' => 'Token CSRF inválido',
            ]);
        }

        $data = [
            'nome' => $this->post('nome'),
            'cnpj' => $this->post('cnpj'),
            'cpf' => $this->post('cpf'),
            'email' => $this->post('email'),
            'telefone' => $this->post('telefone'),
            'celular' => $this->post('celular'),
            'cep' => $this->post('cep'),
            'logradouro' => $this->post('logradouro'),
            'numero' => $this->post('numero'),
            'complemento' => $this->post('complemento'),
            'bairro' => $this->post('bairro'),
            'cidade' => $this->post('cidade'),
            'uf' => $this->post('uf'),
            'status' => (int) $this->post('status', 1),
        ];

        try {
            $this->service->create($data);
            return $this->redirect($this->baseUrl . '/clientes');
        } catch (\Throwable $e) {
            return $this->view('cliente/create', [
                'title' => 'Novo Cliente',
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    public function edit($id): Response
    {
        $cliente = $this->service->find($id);

        if (!$cliente) {
            return $this->redirect($this->baseUrl . '/clientes');
        }

        return $this->view('cliente/edit', [
            'title' => 'Editar Cliente',
            'cliente' => $cliente,
        ]);
    }

    public function update($id): Response
    {
        $csrfToken = $this->post('_csrf_token');
        
        if (!Csrf::validate($csrfToken)) {
            return $this->view('cliente/edit', [
                'title' => 'Editar Cliente',
                'cliente' => $this->service->find($id),
                'error' => 'Token CSRF inválido',
            ]);
        }

        $data = [
            'nome' => $this->post('nome'),
            'cnpj' => $this->post('cnpj'),
            'cpf' => $this->post('cpf'),
            'email' => $this->post('email'),
            'telefone' => $this->post('telefone'),
            'celular' => $this->post('celular'),
            'cep' => $this->post('cep'),
            'logradouro' => $this->post('logradouro'),
            'numero' => $this->post('numero'),
            'complemento' => $this->post('complemento'),
            'bairro' => $this->post('bairro'),
            'cidade' => $this->post('cidade'),
            'uf' => $this->post('uf'),
            'status' => (int) $this->post('status', 1),
        ];

        try {
            $this->service->update($id, $data);
            return $this->redirect($this->baseUrl . '/clientes');
        } catch (\Throwable $e) {
            return $this->view('cliente/edit', [
                'title' => 'Editar Cliente',
                'cliente' => $this->service->find($id),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id): Response
    {
        $csrfToken = $this->post('_csrf_token');
        
        if (!Csrf::validate($csrfToken)) {
            return $this->json(['error' => 'Token CSRF inválido'], 400);
        }

        try {
            $this->service->delete($id);
            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    public function toggle($id): Response
    {
        $csrfToken = $this->post('_csrf_token');
        
        if (!Csrf::validate($csrfToken)) {
            return $this->json(['error' => 'Token CSRF inválido'], 400);
        }

        try {
            $cliente = $this->service->find($id);
            if (!$cliente) {
                return $this->json(['error' => 'Cliente não encontrado'], 404);
            }
            
            $newStatus = $cliente['status'] == 1 ? 0 : 1;
            
            \App\Database\Connection::exec(
                "UPDATE clientes SET status = ? WHERE id = ?",
                [$newStatus, $id]
            );
            
            return $this->json(['success' => true, 'status' => $newStatus]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}