# Quick Start - ConectaFramework

## Instalação

```bash
# 1. Instalar dependências
composer install

# 2. Criar banco de dados
mysql -u root -p -e "CREATE DATABASE novoframework"

# 3. Importar schema
mysql -u root -p novoframework < database/schema.sql

# 4. Copiar .env
cp .env.example .env
```

## Configuração (.env)

```env
APP_ENV=local
BASE_URL=http://localhost/novoframework/public

DB_LOCAL_HOST=localhost
DB_LOCAL_PORT=3306
DB_LOCAL_NAME=novoframework
DB_LOCAL_USER=root
DB_LOCAL_PASS=sua_senha
```

## Servir

```bash
# Development
php -S localhost:8000 -t public

# Ou com Apache (XAMPP/WAMP)
# Acesse: http://localhost/novoframework/public
```

## Criar Novo Módulo

### 1. Tabela
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Repository
```php
// src/Repository/ProductRepository.php
<?php
namespace App\Repository;
class ProductRepository extends BaseRepository
{
    protected string $table = 'products';
    protected array $fillable = ['name', 'description', 'price', 'status'];
}
```

### 3. Service
```php
// src/Service/ProductService.php
<?php
namespace App\Service;
use App\Repository\ProductRepository;
class ProductService extends BaseService
{
    protected string $entityName = 'Product';
    
    public function __construct()
    {
        $repo = new ProductRepository();
        parent::__construct($repo);
    }
    
    public function create(array $data): int
    {
        $this->validateRequired($data, ['name']);
        return $this->repository->create($this->sanitize($data));
    }
}
```

### 4. Controller
```php
// src/Controllers/ProductController.php
<?php
namespace App\Controllers;
use App\Http\Controller;
use App\Core\Response;
use App\Service\ProductService;

class ProductController extends Controller
{
    private ProductService $service;
    
    public function __construct($request)
    {
        parent::__construct($request);
        $this->service = new ProductService();
    }
    
    public function index(): Response
    {
        return $this->view('product/index', [
            'products' => $this->service->paginate(1, 15)
        ]);
    }
}
```

### 5. Views
```php
// views/product/index.php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>
<header id="topbar">...
<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>
<div id="main">
  <div class="content">
    <table class="table-default">...
  </div>
</div>
<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
```

### 6. Rotas (index.php)
```php
$app->router()->group('/products', function($router) {
    $router->get('/', [ProductController::class, 'index']);
    $router->get('/create', [ProductController::class, 'create']);
    $router->post('/store', [ProductController::class, 'store']);
}, [\App\Http\Middleware\AuthMiddleware::class]);
```

## Testar API

```bash
curl http://localhost/novoframework/public/test
```

## Comandos Úteis

```bash
# Verificar conexão
php -r "require 'vendor/autoload.php'; print_r(\App\Database\Connection::testConnection());"

# Ver rotas (adicione temporariamente)
$app->router()->printRoutes();
```

## Estrutura de Arquivos

```
novoframework/
├── public/
│   ├── index.php       # Entry point + rotas
│   └── css/styles.css  # CSS único
├── src/
│   ├── Controllers/   # App\Controllers\*Controller
│   ├── Service/       # App\Service\*Service
│   ├── Repository/    # App\Repository\*Repository
│   └── Core/          # Router, Application, etc
├── views/
│   ├── layout/        # header, sidebar, footer
│   └── modulo/        # index, create, edit
├── config/app.php      # Tema, name, version
└── .env             # Configurações
```

## Temas

Alterar em `config/app.php`:

```php
'theme' => [
    'active' => 'blue',  // default | pink | blue | green | dark
    ...
]
```

## RBAC

| Role | Permissões |
|------|----------|
| guest | Visitante |
| user | Básico |
| manager | Relatórios |
| admin | Tudo |

## Erros Comuns

| Erro | Solução |
|------|---------|
| 404 | Verificar BASE_URL no .env |
| Class not found | `composer dump-autoload` |
| Table not found | Executar migrations |
| Login não funciona | Verificar session no PHP |

---

**Pronto!** O framework está configurado e funcionando.