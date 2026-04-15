<?php

namespace App\Core;

class Application
{
    private static ?Application $instance = null;
    private Router $router;
    private Request $request;
    private array $middleware = [];
    private bool $booted = false;
    private array $config = [];

    public function __construct()
    {
        self::$instance = $this;
        $this->router = new Router();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot(): void
    {
        if ($this->booted) return;
        
        Env::load();
        ErrorHandler::register();
        Debug::start();
        Session::start();
        
        // Carregar config/app.php
        $this->loadConfig();
        
        $this->booted = true;
    }
    
    private function loadConfig(): void
    {
        $configFile = dirname(__DIR__, 2) . '/config/app.php';
        
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        }
    }
    
    public function getConfig(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? $default;
    }
    
    public function getTheme(): array
    {
        $themeActive = $this->config['theme']['active'] ?? 'default';
        $themes = $this->config['theme']['themes'] ?? [];
        
        return $themes[$themeActive] ?? $themes['default'] ?? [];
    }
    
    public function getAppName(): string
    {
        return $this->config['name'] ?? 'App';
    }
    
    public function getAppLogo(): string
    {
        return $this->config['logo'] ?? '';
    }
    
    public function getAppLogoText(): string
    {
        return $this->config['logo_text'] ?? 'A';
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function run(): void
    {
        $this->boot();
        
        // Registrar rotas ANTES de criar request
        $this->registerRoutes();
        
        error_log("ROUTES REGISTERED: " . count($this->router->getRoutes()));
        
        $this->request = new Request();
        
        $response = $this->router->dispatch($this->request);
        $response->send();
    }

    public function useMiddleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    protected function registerRoutes(): void
    {
    }

    public function isLocal(): bool
    {
        return Env::get('APP_ENV') === 'local';
    }

    public function baseUrl(): string
    {
        return Env::get('BASE_URL', '');
    }

    public function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    public function view(string $view, array $data = [], int $statusCode = 200): Response
    {
        // Adicionar config automaticamente para todas as views
        $data['appName'] = $this->getAppName();
        $data['appLogo'] = $this->getAppLogo();
        $data['appLogoText'] = $this->getAppLogoText();
        $data['appTitle'] = $this->config['title'] ?? 'App';
        $data['appVersion'] = $this->config['version'] ?? '1.0.0';
        $data['theme'] = $this->getTheme();
        
        extract($data);
        
        $viewFile = dirname(__DIR__, 2) . "/views/$view.php";
        
        if (!file_exists($viewFile)) {
            return Response::json(['error' => 'View não encontrada: ' . $view], 404);
        }
        
        ob_start();
        include $viewFile;
        $html = ob_get_clean();
        
        return Response::html($html, $statusCode);
    }

    public function json(mixed $data): Response
    {
        return Response::json($data);
    }
}

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = (int) Env::get('SESSION_LIFETIME', 120);
            ini_set('session.cookie_lifetime', $lifetime * 60);
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }
}