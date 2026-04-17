<?php

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $headers;
    private string $body;
    private string $uri;
    private string $method;
    private bool $isLocal;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
        $this->body = file_get_contents('php://input');
        $this->uri = $this->server['REQUEST_URI'] ?? '/';
        $this->method = $this->server['REQUEST_METHOD'] ?? 'GET';
        $this->isLocal = $this->detectEnvironment();
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            }
        }
        return $headers;
    }

    private function detectEnvironment(): bool
    {
        $baseUrl = Env::get('BASE_URL', '');
        if (empty($baseUrl)) return true;
        
        $host = $this->server['HTTP_HOST'] ?? $this->server['SERVER_NAME'] ?? '';
        return str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1');
    }

    public function get(?string $key = null, $default = null)
    {
        if ($key === null) return $this->get;
        return $this->get[$key] ?? $default;
    }

    public function post(?string $key = null, $default = null)
    {
        if ($key === null) return $this->post;
        return $this->post[$key] ?? $default;
    }

    public function input(?string $key = null, $default = null)
    {
        if ($key === null) return $this->body;
        
        $data = json_decode($this->body, true) ?? [];
        return $data[$key] ?? $default;
    }

    public function header(string $key, $default = null)
    {
        return $this->headers[$key] ?? $default;
    }

    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function isLocal(): bool
    {
        return $this->isLocal;
    }

    public function isProduction(): bool
    {
        return !$this->isLocal;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isAjax(): bool
    {
        return ($this->headers['X-Requested-With'] ?? '') === 'XMLHttpRequest';
    }

    public function ip(): string
    {
        return $this->headers['X-Forwarded-For'] ?? 
               $this->server['REMOTE_ADDR'] ?? 
               '0.0.0.0';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
}