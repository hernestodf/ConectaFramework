<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $groups = [];
    private string $groupPrefix = '';
    private array $groupMiddleware = [];
    
    public function get(string $path, array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, array $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, array $handler): self
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, array $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    public function any(string $path, array $handler): self
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler);
        }
        return $this;
    }

    public function group(string $prefix, callable $callback, array $middleware = []): self
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;
        
        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);
        
        $callback($this);
        
        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
        
        return $this;
    }

    private function addRoute(string $method, string $path, array $handler): self
    {
        $fullPath = $this->groupPrefix . $path;
        
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => $this->groupMiddleware,
            'originalPath' => $path,
        ];
        
        return $this;
    }

    public function middleware(array $middleware): self
    {
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);
        return $this;
    }

public function dispatch(Request $request): Response
    {
        $uri = $request->uri();
        $method = $request->method();
        
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) $uri = '/';
        
        // Remover prefixo do caminho baseado no BASE_URL do .env
        $baseUrl = Env::get('BASE_URL', '');
        $basePath = parse_url($baseUrl, PHP_URL_PATH);
        
        if (!empty($basePath) && $basePath !== '/') {
            $basePath = rtrim($basePath, '/');
            $uri = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $uri) ?: '/';
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            
            $pattern = $this->convertToRegex($route['path']);
            
            $match = preg_match($pattern, $uri, $matches);
            
            if ($match) {
                array_shift($matches);
                $params = array_values($matches);
                
                // Sem middleware - executa direto
                $middlewares = $route['middleware'] ?? [];
                if (empty($middlewares)) {
                    return $this->execute($route, $request, $params);
                }
                
                // Executar chain de middlewares
                $index = 0;
                
                $run = function($req) use (&$run, &$index, $middlewares, $route, $params) {
                    if ($index >= count($middlewares)) {
                        return $this->execute($route, $req, $params);
                    }
                    $middleware = new $middlewares[$index++]();
                    return $middleware->handle($req, $run);
                };
                
                return $run($request);
            }
        }
        
        $errorController = new \App\Http\ErrorController();
        return $errorController->notFound();
    }

    private function convertToRegex(string $path): string
    {
        $path = rtrim($path, '/');
        $path = $path ?: '/';
        $pattern = preg_replace('/\/\{(\w+)\}/', '/(?<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '(?:/)?$#';
        return $pattern;
    }

    private function execute(array $route, Request $request, array $params): Response
    {
        [$controller, $action] = $route['handler'];
        
        if (!class_exists($controller)) {
            return Response::json(['error' => 'Controller não encontrado'], 500);
        }
        
        $controllerInstance = new $controller($request);
        
        if (!method_exists($controllerInstance, $action)) {
            return Response::json(['error' => 'Método não encontrado'], 500);
        }
        
        return $controllerInstance->$action(...$params);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function printRoutes(): void
    {
        echo "<pre>";
        foreach ($this->routes as $route) {
            echo "{$route['method']} {$route['path']} => " . implode('@', $route['handler']) . "\n";
        }
        echo "</pre>";
    }
}