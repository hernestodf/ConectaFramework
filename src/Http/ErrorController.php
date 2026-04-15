<?php

namespace App\Http;

use App\Core\Logger;

class ErrorController extends Controller
{
    private bool $isLocal;

    public function __construct()
    {
        $this->isLocal = \App\Core\Env::get('APP_ENV') === 'local';
    }

    public function notFound(): \App\Core\Response
    {
        $title = 'Página Não Encontrada';
        $message = 'A página que você está procurando não existe.';
        
        Logger::warning('404 Not Found', [
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);

        if ($this->isLocal) {
            return $this->view('errors/404', compact('title', 'message'), 404);
        }

        return $this->view('errors/404', [
            'title' => $title,
            'message' => 'Página não encontrada',
            'simple' => true,
        ], 404);
    }

    public function serverError(string $errorId = '', string $message = ''): \App\Core\Response
    {
        $title = 'Erro Interno';
        
        Logger::error('500 Server Error', [
            'errorId' => $errorId,
            'message' => $message,
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
        ]);

        if ($this->isLocal) {
            return $this->view('errors/500', compact('title', 'errorId', 'message'), 500);
        }

        return $this->view('errors/500', [
            'title' => $title,
            'errorId' => $errorId,
            'simple' => true,
        ], 500);
    }

    public function forbidden(): \App\Core\Response
    {
        Logger::warning('403 Forbidden', [
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);

        $title = 'Acesso Negado';
        $message = 'Você não tem permissão para acessar este recurso.';

        if ($this->isLocal) {
            return $this->view('errors/500', compact('title', 'message'), 403);
        }

        return $this->view('errors/500', [
            'title' => $title,
            'message' => 'Acesso negado',
            'simple' => true,
        ], 403);
    }

    public function unauthorized(): \App\Core\Response
    {
        $title = 'Não Autorizado';
        $message = 'Você precisa fazer login para acessar este recurso.';

        if ($this->isLocal) {
            return $this->view('errors/500', compact('title', 'message'), 401);
        }

        return $this->view('errors/500', [
            'title' => $title,
            'message' => 'Não autorizado',
            'simple' => true,
        ], 401);
    }
}