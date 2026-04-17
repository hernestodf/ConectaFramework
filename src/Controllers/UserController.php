<?php

namespace App\Controllers;

use App\Http\Controller;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Env;
use App\Service\UserService;

class UserController extends Controller
{
    private UserService $service;
    private string $baseUrl;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->service = new UserService();
        $this->baseUrl = rtrim(Env::get('BASE_URL', ''), '/');
    }

    public function index(): Response
    {
        $page = (int) $this->get('page', 1);
        $data = $this->service->paginate($page, 15);

        return $this->view('user/index', [
            'title' => 'Usuários',
            'users' => $data['data'],
            'pagination' => $data['pagination'],
        ]);
    }

    public function create(): Response
    {
        return $this->view('user/create', [
            'title' => 'Novo Usuário',
        ]);
    }

    public function store(): Response
    {
        $csrfToken = $this->post('_csrf_token');
        
        if (!Csrf::validate($csrfToken)) {
            return $this->view('user/create', [
                'title' => 'Novo Usuário',
                'error' => 'Token CSRF inválido',
            ]);
        }

        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'telefone' => $this->post('telefone'),
            'celular' => $this->post('celular'),
            'cep' => $this->post('cep'),
            'password' => $this->post('password'),
            'role' => $this->post('role', 'user'),
            'status' => (int) $this->post('status', 1),
        ];

        try {
            $this->service->create($data);
            return $this->redirect($this->baseUrl . '/users');
        } catch (\Throwable $e) {
            return $this->view('user/create', [
                'title' => 'Novo Usuário',
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    public function edit($id): Response
    {
        $user = $this->service->find($id);

        if (!$user) {
            return $this->redirect($this->baseUrl . '/users');
        }

        return $this->view('user/edit', [
            'title' => 'Editar Usuário',
            'user' => $user,
        ]);
    }

    public function update($id): Response
    {
        $csrfToken = $this->post('_csrf_token');
        
        if (!Csrf::validate($csrfToken)) {
            return $this->view('user/edit', [
                'title' => 'Editar Usuário',
                'user' => $this->service->find($id),
                'error' => 'Token CSRF inválido',
            ]);
        }

        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'telefone' => $this->post('telefone'),
            'celular' => $this->post('celular'),
            'cep' => $this->post('cep'),
            'role' => $this->post('role', 'user'),
            'status' => (int) $this->post('status', 1),
        ];

        $password = $this->post('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        try {
            $this->service->update($id, $data);
            return $this->redirect($this->baseUrl . '/users');
        } catch (\Throwable $e) {
            return $this->view('user/edit', [
                'title' => 'Editar Usuário',
                'user' => $this->service->find($id),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function show($id): Response
    {
        $user = $this->service->find($id);

        if (!$user) {
            return $this->redirect($this->baseUrl . '/users');
        }

        return $this->view('user/show', [
            'title' => 'Ver Usuário',
            'user' => $user,
        ]);
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
            $user = $this->service->find($id);
            if (!$user) {
                return $this->json(['error' => 'Usuário não encontrado'], 404);
            }
            
            $newStatus = $user['status'] == 1 ? 0 : 1;
            
            // Direct database update to bypass validation
            \App\Database\Connection::exec(
                "UPDATE users SET status = ? WHERE id = ?",
                [$newStatus, $id]
            );
            
            return $this->json(['success' => true, 'status' => $newStatus]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}