# ConectaFramework

Framework PHP MVC com RBAC, roteamento por grupos e sistema de temas.

## Estrutura

```
conectaframework/
├── src/
│   ├── Controllers/      # Controllers (HomeController, AuthController, AdminController)
│   ├── Service/        # Services (ProductService, BaseService)
│   ├── Repository/     # Repositories (BaseRepository, UserRepository)
│   ├── Http/         # Middlewares e Controller base
│   ├── Database/     # Connection (PDO)
│   ├── Auth/        # Rbac (guest, user, manager, admin)
│   └── Core/        # Application, Router, Request, Response
├── views/
│   ├── layout/      # header.php, sidebar.php, footer.php
│   ├── home/       # index.php
│   ├── admin/      # index.php
│   └── errors/    # 404.php, 500.php, debug.php
├── public/
│   ├── index.php  # Entry point
│   └── css/       # styles.css
└── config/
    └── app.php   # Configurações do app
```

---

## Guia: Criar Novo Módulo/CRUD

Este guia mostra como criar um novo módulo completo (ex: produtos, clientes, ordens, etc) no framework.

### Fluxo de Dados

```
URL Request
    ↓
Controller (recebe requisição)
    ↓
Service (regras de negócio)
    ↓
Repository (acesso ao banco)
    ↓
Database (MySQL)
```

### Estrutura de Arquivos

Para cada novo módulo, você vai criar:

```
src/Controllers/ProductController.php    # Controller (recebe requisição)
src/Service/ProductService.php           # Service (regras de negócio)
src/Repository/ProductRepository.php   # Repository (banco de dados)
views/product/                        # Views (interface)
    ├── index.php    # Listar todos
    ├── create.php  # Formulário criar
    ├── edit.php   # Formulário editar
    └── show.php   # Ver detalhes
```

---

### Passo 1: Criar Tabela no Banco

Execute no MySQL:

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

### Passo 2: Criar Repository

**Arquivo:** `src/Repository/ProductRepository.php`

```php
<?php

namespace App\Repository;

class ProductRepository extends BaseRepository
{
    protected string $table = 'products';
    
    protected array $fillable = ['name', 'description', 'price', 'status'];
}
```

**Métodos disponíveis do BaseRepository:**

| Método | Descrição | Exemplo |
|-------|----------|---------|
| `all()` | Listar todos | `$repo->all()` |
| `find(id)` | Buscar por ID | `$repo->find(1)` |
| `findBy('field', value)` | Buscar por campo | `$repo->findBy('email', 'a@b.com')` |
| `findByEmail(value)` | Buscar dinâmico | `$repo->findByEmail('a@b.com')` |
| `create(data)` | Criar registro | `$repo->create(['name' => 'Produto'])` |
| `update(id, data)` | Atualizar | `$repo->update(1, ['name' => 'Novo'])` |
| `delete(id)` | Deletar | `$repo->delete(1)` |
| `paginate(page, perPage)` | Paginação | `$repo->paginate(1, 15)` |

---

### Passo 3: Criar Service (Regras de Negócio)

**Arquivo:** `src/Service/ProductService.php`

```php
<?php

namespace App\Service;

use App\Repository\ProductRepository;

class ProductService extends BaseService
{
    public function __construct(?ProductRepository $repository = null)
    {
        $repository = $repository ?? new ProductRepository();
        parent::__construct($repository);
    }

    // Criar com validações
    public function create(array $data): int
    {
        $this->validateForCreate($data);
        $data = $this->sanitize($data);
        
        $data['price'] = $this->formatPrice($data['price'] ?? 0);
        $data['status'] = $data['status'] ?? 1;
        
        $id = $this->repository->create($data);
        
        $this->log('info', "Criado com ID {$id}", $data);
        
        return $id;
    }

    // Atualizar com validações
    public function update(int $id, array $data): int
    {
        $this->findOrFail($id);
        $this->validateForUpdate($data);
        $data = $this->sanitize($data);
        
        if (isset($data['price'])) {
            $data['price'] = $this->formatPrice($data['price']);
        }
        
        $result = $this->repository->update($id, $data);
        
        $this->log('info', "Atualizado ID {$id}", $data);
        
        return $result;
    }

    // Deletar
    public function delete(int $id): int
    {
        $this->findOrFail($id);
        
        $result = $this->repository->delete($id);
        
        $this->log('info', "Deletado ID {$id}");
        
        return $result;
    }

    // Ativar produto
    public function activate(int $id): int
    {
        $this->findOrFail($id);
        $result = $this->repository->update($id, ['status' => 1]);
        $this->log('info', "Ativado ID {$id}");
        return $result;
    }

    // Desativar produto
    public function deactivate(int $id): int
    {
        $this->findOrFail($id);
        $result = $this->repository->update($id, ['status' => 0]);
        $this->log('info', "Desativado ID {$id}");
        return $result;
    }

    // Buscar ativos
    public function getActive(): array
    {
        return $this->repository->findBy('status', 1);
    }

    // Buscar por termo
    public function search(string $term): array
    {
        $all = $this->repository->all();
        
        return array_filter($all, function($item) use ($term) {
            $term = strtolower($term);
            return str_contains(strtolower($item['name'] ?? ''), $term);
        });
    }

    // Formatar preço
    protected function formatPrice($price): float
    {
        if (is_string($price)) {
            $price = str_replace(['R$', ',', ' '], '', $price);
        }
        return (float) $price;
    }

    // Validar para criar
    protected function validateForCreate(array $data): void
    {
        $this->validateRequired($data, ['name']);
        
        if (isset($data['price']) && $data['price'] < 0) {
            throw new \Exception('Preço não pode ser negativo');
        }
    }

    // Validar para atualizar
    protected function validateForUpdate(array $data): void
    {
        if (isset($data['price']) && $data['price'] < 0) {
            throw new \Exception('Preço não pode ser negativo');
        }
    }
}
```

**Métodos disponíveis do ProductService:**

| Método | Descrição | Exemplo |
|-------|----------|---------|
| `all()` | Listar todos | `$service->all()` |
| `find(id)` | Buscar por ID | `$service->find(1)` |
| `create(data)` | Criar com validação | `$service->create(['name' => 'Produto'])` |
| `update(id, data)` | Atualizar com validação | `$service->update(1, ['name' => 'Novo'])` |
| `delete(id)` | Deletar | `$service->delete(1)` |
| `activate(id)` | Ativar | `$service->activate(1)` |
| `deactivate(id)` | Desativar | `$service->deactivate(1)` |
| `getActive()` | Listar ativos | `$service->getActive()` |
| `search(term)` | Buscar | `$service->search('termo')` |
| `findOrFail(id)` | Buscar ou exception | `$service->findOrFail(1)` |

**Métodos herdados do BaseService:**

| Método | Descrição |
|--------|----------|
| `all()` | Listar todos |
| `find(id)` | Buscar por ID |
| `paginate(page, perPage)` | Paginação |
| `findOrFail(id)` | Buscar ou lançar exception |
| `validateRequired(data, fields)` | Validar campos obrigatórios |
| `sanitize(data)` | Limpar dados |

---

### Passo 4: Criar Controller (Usando Service)

**Arquivo:** `src/Controllers/ProductController.php`

```php
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

    // GET /products - Listar todos
    public function index(): Response
    {
        $page = (int) $this->get('page', 1);
        $data = $this->service->paginate($page, 15);

        return $this->view('product/index', [
            'title' => 'Produtos',
            'products' => $data['data'],
            'pagination' => $data['pagination'],
        ]);
    }

    // GET /products/create - Formulário criar
    public function create(): Response
    {
        return $this->view('product/create', [
            'title' => 'Novo Produto',
        ]);
    }

    // POST /products/store - Salvar
    public function store(): Response
    {
        try {
            $data = [
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'price' => $this->post('price'),
                'status' => (int) $this->post('status', 1),
            ];

            $this->service->create($data);

            return $this->redirect($this->baseUrl . '/products');
        } catch (\Exception $e) {
            return $this->view('product/create', [
                'title' => 'Novo Produto',
                'error' => $e->getMessage(),
            ]);
        }
    }

    // GET /products/edit/{id} - Formulário editar
    public function edit($id): Response
    {
        try {
            $product = $this->service->findOrFail($id);

            return $this->view('product/edit', [
                'title' => 'Editar Produto',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return $this->redirect($this->baseUrl . '/products');
        }
    }

    // POST /products/update/{id} - Atualizar
    public function update($id): Response
    {
        try {
            $data = [
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'price' => $this->post('price'),
                'status' => (int) $this->post('status', 1),
            ];

            $this->service->update($id, $data);

            return $this->redirect($this->baseUrl . '/products');
        } catch (\Exception $e) {
            return $this->view('product/edit', [
                'title' => 'Editar Produto',
                'product' => $this->service->find($id),
                'error' => $e->getMessage(),
            ]);
        }
    }

    // GET /products/show/{id} - Ver detalhes
    public function show($id): Response
    {
        try {
            $product = $this->service->findOrFail($id);

            return $this->view('product/show', [
                'title' => $product['name'],
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return $this->redirect($this->baseUrl . '/products');
        }
    }

    // POST /products/delete/{id} - Deletar
    public function delete($id): Response
    {
        try {
            $this->service->delete($id);
            return $this->redirect($this->baseUrl . '/products');
        } catch (\Exception $e) {
            return $this->redirect($this->baseUrl . '/products');
        }
    }

    // POST /products/activate/{id} - Ativar
    public function activate($id): Response
    {
        $this->service->activate($id);
        return $this->redirect($this->baseUrl . '/products');
    }

    // POST /products/deactivate/{id} - Desativar
    public function deactivate($id): Response
    {
        $this->service->deactivate($id);
        return $this->redirect($this->baseUrl . '/products');
    }
}
```

---

### Passo 5: Criar Views

**Arquivo:** `views/product/index.php` (Listar todos)

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Produtos</span>
      <span class="sep">/</span>
      <span class="cur">Lista</span>
    </div>
  </div>
  <div class="tb-right">
    <a href="<?= $baseUrl ?>/products/create" class="btn btn-cyan">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Novo Produto
    </a>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <div class="card">
      <div class="card-head">
        <span class="card-title">Lista de Produtos</span>
      </div>
      <div class="card-body" style="padding:0">
        <table class="table-default">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Preço</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td>R$ <?= number_format($p['price'], 2, ',', '.') ?></td>
              <td>
                <span class="badge <?= $p['status'] ? 'green' : 'red' ?>">
                  <?= $p['status'] ? 'Ativo' : 'Inativo' ?>
                </span>
              </td>
              <td>
                <a href="<?= $baseUrl ?>/products/edit/<?= $p['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
                <form method="POST" action="<?= $baseUrl ?>/products/delete/<?= $p['id'] ?>" style="display:inline">
                  <button type="submit" class="btn btn-sm btn-red" onclick="return confirm('Confirmar?')">Excluir</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
```

**Arquivo:** `views/product/create.php` (Formulário criar)

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Produtos</span>
      <span class="sep">/</span>
      <span class="cur">Novo</span>
    </div>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <div class="card">
      <div class="card-head">
        <span class="card-title">Novo Produto</span>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/products/store">
          <div class="form-group">
            <label class="form-label">Nome</label>
            <input type="text" name="name" class="form-input" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Descrição</label>
            <textarea name="description" class="form-input" rows="4"></textarea>
          </div>
          
          <div class="form-group">
            <label class="form-label">Preço</label>
            <input type="number" name="price" class="form-input" step="0.01" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
              <option value="1">Ativo</option>
              <option value="0">Inativo</option>
            </select>
          </div>
          
          <div class="form-actions">
            <a href="<?= $baseUrl ?>/products" class="btn btn-gray">Cancelar</a>
            <button type="submit" class="btn btn-cyan">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
```

---

### Passo 6: Registrar Rotas

**Arquivo:** `public/index.php`

```php
<?php

use App\Core\Application;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = Application::getInstance();

// Rotas públicas
$app->router()->get('/', [App\Controllers\HomeController::class, 'index']);
$app->router()->get('/home', [App\Controllers\HomeController::class, 'index']);

// Grupo de autenticação
$app->router()->group('/auth', function($router) {
    $router->get('/login', [App\Controllers\AuthController::class, 'login']);
    $router->post('/login', [App\Controllers\AuthController::class, 'doLogin']);
    $router->post('/logout', [App\Controllers\AuthController::class, 'logout']);
});

// ROTAS DE PRODUTOS (protegidas com autenticação)
$app->router()->group('/products', function($router) {
    $router->get('/', [App\Controllers\ProductController::class, 'index']);
    $router->get('/create', [App\Controllers\ProductController::class, 'create']);
    $router->post('/store', [App\Controllers\ProductController::class, 'store']);
    $router->get('/edit/{id}', [App\Controllers\ProductController::class, 'edit']);
    $router->post('/update/{id}', [App\Controllers\ProductController::class, 'update']);
    $router->get('/show/{id}', [App\Controllers\ProductController::class, 'show']);
    $router->post('/delete/{id}', [App\Controllers\ProductController::class, 'delete']);
}, [App\Http\Middleware\AuthMiddleware::class]);

// Grupo admin
$app->router()->group('/admin', function($router) {
    $router->get('/', [App\Controllers\AdminController::class, 'index']);
}, [App\Http\Middleware\AuthMiddleware::class]);

$app->run();
```

---

## Métodos do Controller

| Método | Descrição | Exemplo |
|-------|----------|--------|
| `$this->view('view', data, status)` | Renderizar view | `$this->view('product/index', ['products' => $data])` |
| `$this->json(data)` | Retornar JSON | `$this->json(['success' => true])` |
| `$this->redirect(url)` | Redirecionar | `$this->redirect('/products')` |
| `$this->back()` | Voltar | `$this->back()` |
| `$this->input('key')` | GET/POST | `$this->input('name')` |
| `$this->get('key')` | Apenas GET | `$this->get('page', 1)` |
| `$this->post('key')` | Apenas POST | `$this->post('name')` |

---

## RBAC - Permissões

O framework tem 4 roles integrados:

| Role | Permissões |
|-----|-----------|
| `guest` | Visitante (não logado) |
| `user` | Usuário comum |
| `manager` | Gerente (acesso completo) |
| `admin` | Administrador (tudo) |

**Verificar permissão:**

```php
use App\Auth\Rbac;

// Verificar papel
Rbac::hasRole('admin');     // true/false
Rbac::isAdmin();           // true/false
Rbac::isGuest();           // true/false

// Verificar permissão
Rbac::check('dashboard');   // true/false
```

---

## Camada Services

O ConectaFramework possui uma camada de Services para regras de negócio em projetos grandes.

### Estrutura

```
src/Service/
├── BaseService.php      # Classe base com helpers
└── ProductService.php # Exemplo de service
```

### BaseService

Classe abstrata com métodos utilitários:

```php
use App\Service\ProductService;

$service = new ProductService();
```

| Método | Descrição |
|--------|----------|
| `all()` | Listar todos |
| `find(id)` | Buscar por ID |
| `paginate(page, perPage)` | Paginação |
| `findOrFail(id)` | Buscar ou exception |
| `validateRequired(data, fields)` | Validar campos |
| `sanitize(data)` | Limpar dados |

### ProductService

Service com regras de negócio:

```php
$service = new ProductService();

// Criar
$id = $service->create(['name' => 'Produto', 'price' => 99.90]);

// Atualizar
$service->update($id, ['name' => 'Novo nome']);

// Deletar
$service->delete($id);

// Ativar/Desativar
$service->activate($id);
$service->deactivate($id);

// Buscar ativos
$ativos = $service->getActive();

// Buscar por termo
$result = $service->search('termo');
```

### Criar Novo Service

Para criar um novo service (ex: CustomerService):

**1. Repository:** `src/Repository/CustomerRepository.php`
```php
class CustomerRepository extends BaseRepository
{
    protected string = 'customers';
    protected array $fillable = ['name', 'email', 'phone'];
}
```

**2. Service:** `src/Service/CustomerService.php`
```php
class CustomerService extends BaseService
{
    protected string $entityName = 'Customer';

    public function __construct($repo = null)
    {
        $repo = $repo ?? new CustomerRepository();
        parent::__construct($repo);
    }

    protected function validateForCreate(array $data): void
    {
        $this->validateRequired($data, ['name', 'email']);
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Email inválido');
        }
    }
}
```

**3. Controller:** Usar o service:
```php
$service = new CustomerService();
$id = $service->create($data);
```

---

## Middleware

**Proteger rota com autenticação:**

```php
$app->router()->group('/produtos', function($router) {
    $router->get('/', [App\Controllers\ProdutoController::class, 'index']);
}, [App\Http\Middleware\AuthMiddleware::class]);
```

**Criar middleware customizado:**

```php
// src/Http/Middleware/MeuMiddleware.php
<?php

namespace App\Http;

class MeuMiddleware implements Middleware
{
    public function handle(): bool
    {
        // Lógica aqui
        return true; // continua, false = bloqueia
    }
}
```

---

## Configurações

**.env:**

```env
APP_ENV=local
BASE_URL=http://localhost/novoframework/public

# DB Local
DB_LOCAL_HOST=localhost
DB_LOCAL_PORT=3306
DB_LOCAL_NAME=novoframework
DB_LOCAL_USER=root
DB_LOCAL_PASS=senha
```

**config/app.php:**

```php
<?php

return [
    'name' => 'ConectaFramework',
    'title' => 'Dashboard',
    'version' => '1.0.0',
    'logo_text' => 'N',
    'theme' => [
        'active' => 'default',
        'themes' => [
            'default' => [
                'primary' => '#0B6E8C',
                'primary_glow' => 'rgba(11,110,140,0.28)',
                'sidebar_bg' => '#1E1B4B',
                'surface' => '#FCE7F3',
                'background' => '#FDF2F8',
                'text' => '#1E1B4B',
                'text_light' => '#4C1D4E',
                'sidebar_hover' => 'rgba(255,255,255,0.15)',
            ],
            // outros temas: pink, blue, green, dark
        ],
    ],
];
```

---

## Deploy

### Via FTP (deploy/deploy.py)

```bash
# Instalar dependências
pip install -r deploy/requirements.txt

# Executar deploy
python deploy/deploy.py
```

### Dump do Banco

```bash
# Exportar
mysqldump -u root -p senha novoframework > database/dump.sql

# Importar
mysql -u root -p senha novoframework < database/dump.sql
```

---

## Comandos Úteis

```bash
# Testar rota
curl http://localhost/novoframework/public/test

# Testar conexão
php -r "require '/var/www/html/novoframework/vendor/autoload.php'; print_r(\App\Database\Connection::testConnection());"

# Verificar logs
tail -f /var/www/html/novoframework/storage/logs/$(date +%Y-%m-%d).log
```

---

## Layouts Prontos

O framework possui layouts prontos em `docs/layout/` para usar em suas páginas.

### Arquivos Disponíveis

| Arquivo | Descrição |
|--------|-----------|
| `docs/layout/styles.css` | CSS completo (854 linhas) |
| `docs/layout/login.html` | Layout de login |
| `docs/layout/branco.html` | Layout limpo |
| `docs/layout/scripts.js` | Funções JS |

### Como Usar os Layouts

**1. Login Page (docs/layout/login.html)**

Crie a view `views/auth/login.php`:

```php
<?php
$title = 'Login';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $title ?? 'Login' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= $baseUrl ?>/docs/layout/styles.css"/>
  <style>
    /* Seu tema customizado */
    :root {
      --neon-cyan: #0B6E8C;
      --neon-cyan-glow: rgba(11,110,140,0.28);
      --bg-darkest: #FDF2F8;
      --bg-surface: #FCE7F3;
      --bg-card: #FFFFFF;
      --text-1: #1E1B4B;
      --text-2: #1E1B4B;
      --text-3: #4C1D4E;
      --text-4: #6B7280;
      --bg-border: #E5E7EB;
      --bg-border-sub: #F3F4F6;
      --bg-hover: #F9FAFB;
    }
  </style>
</head>
<body>
  <!-- Copie o conteúdo de docs/layout/login.html e personalize -->
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-header">
        <div class="login-logo">
          <!-- Seu logo -->
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
        </div>
        <div class="login-title">ConectaFramework</div>
        <div class="login-sub">Faça login para continuar</div>
      </div>
      <div class="login-body">
        <form method="POST" action="<?= $baseUrl ?>/auth/login">
          <div class="fg">
            <div class="fl">Email</div>
            <input type="email" name="email" class="fi" placeholder="seu@email.com" required>
          </div>
          <div class="fg">
            <div class="fl">Senha</div>
            <input type="password" name="password" class="fi" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn-login">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
```

**2. Página Interna (usando o layout do framework)**

O framework já inclui layout completo em `views/layout/`:

- `views/layout/header.php` - Header com tema
- `views/layout/sidebar.php` - Menu lateral
- `views/layout/footer.php` - Footer com JS

Basta criar suas views seguindo o padrão:

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Módulo</span>
      <span class="sep">/</span>
      <span class="cur">Página</span>
    </div>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <!-- Seu conteúdo aqui -->
  </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
```

### Componentes do Layout

| Componente | Descrição |
|-----------|-----------|
| `header#topbar` | Barra superior com breadcrumb |
| `aside#sidebar` | Menu lateral |
| `div#main` | Área principal de conteúdo |
| `section#sec-nome` | Seção de página |

### Classes de Estilo

| Classe | Descrição |
|--------|-----------|
| `card` | Card com header e body |
| `card-title` | Título do card |
| `btn btn-cyan` | Botão primário |
| `btn btn-gray` | Botão secundário |
| `btn btn-red` | Botão perigo |
| `form-group` | Grupo de formulário |
| `form-label` | Rótulo do campo |
| `form-input` | Campo de entrada |
| `badge green` | Badge sucesso |
| `badge red` | Badge erro |

---

## API - WEB e API Simultaneamente

O framework suporta WEB e API simultaneamente no mesmo projeto.

### WEB vs API

| Aspecto | WEB | API |
|---------|-----|-----|
| Resposta | HTML (view) | JSON (json) |
| Autenticação | Session | Token |
| Retorno | `$this->view()` | `$this->json()` |
| Rota típica | /products | /api/products |

### Criar API

**1. Rotas API:**

```php
// Grupo API
$app->router()->group('/api', function($router) {
    $router->get('/products', [ProductController::class, 'apiIndex']);
    $router->get('/products/{id}', [ProductController::class, 'apiShow']);
    $router->post('/products', [ProductController::class, 'apiStore']);
    $router->put('/products/{id}', [ProductController::class, 'apiUpdate']);
    $router->delete('/products/{id}', [ProductController::class, 'apiDelete']);
});
```

**2. Controller API:**

```php
// GET /api/products - Listar todos
public function apiIndex(): Response
{
    $products = $this->service->all();
    return $this->json(['success' => true, 'data' => $products]);
}

// GET /api/products/{id} - Buscar um
public function apiShow($id): Response
{
    try {
        $product = $this->service->findOrFail($id);
        return $this->json(['success' => true, 'data' => $product]);
    } catch (\Exception $e) {
        return $this->json(['success' => false, 'error' => 'Não encontrado'], 404);
    }
}

// POST /api/products - Criar
public function apiStore(): Response
{
    try {
        $data = [
            'name' => $this->post('name'),
            'price' => $this->post('price'),
        ];
        $id = $this->service->create($data);
        return $this->json(['success' => true, 'data' => ['id' => $id]], 201);
    } catch (\Exception $e) {
        return $this->json(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

// PUT /api/products/{id} - Atualizar
public function apiUpdate($id): Response
{
    try {
        $data = [
            'name' => $this->post('name'),
            'price' => $this->post('price'),
        ];
        $this->service->update($id, $data);
        return $this->json(['success' => true, 'message' => 'Atualizado']);
    } catch (\Exception $e) {
        return $this->json(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

// DELETE /api/products/{id} - Deletar
public function apiDelete($id): Response
{
    try {
        $this->service->delete($id);
        return $this->json(['success' => true, 'message' => 'Deletado']);
    } catch (\Exception $e) {
        return $this->json(['success' => false, 'error' => $e->getMessage()], 400);
    }
}
```

### Códigos HTTP

| Código | Significado |
|--------|------------|
| 200 | OK |
| 201 | Criado |
| 400 | Erro na requisição |
| 404 | Não encontrado |

### Métodos HTTP

| Método | Descrição |
|--------|----------|
| GET | Buscar/ler |
| POST | Criar |
| PUT | Atualizar |
| DELETE | Deletar |

### Testar API

```bash
# GET
curl http://localhost/novoframework/public/api/products

# POST
curl -X POST http://localhost/novoframework/public/api/products \
  -d "name=Produto&price=99.90"

# PUT
curl -X PUT http://localhost/novoframework/public/api/products/1 \
  -d "name=NovoNome"

# DELETE
curl -X DELETE http://localhost/novoframework/public/api/products/1
```

---

## Proteção CSRF

O framework possui proteção CSRF nativa para segurança.

### O que é CSRF

CSRF previne ataques que enviam formulários em nome do usuário sem consentimento.

### Arquivo: src/Core/Csrf.php

```php
use App\Core\Csrf;

// Gerar token
Csrf::generate();

// Validar token
Csrf::validate($token);

// Obter token atual
Csrf::getToken()
```

### Como Usar em Forms

```php
// Campo hidden em TODO form:
<input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>"/>
```

### Como Validar em Controller

```php
public function store(): Response
{
    $csrfToken = $this->post('_csrf_token');
    
    if (!\App\Core\Csrf::validate($csrfToken)) {
        return $this->json(['error' => 'Token inválido'], 403);
    }
    
    // Continuar...
}
```

### Como Validar em API

```php
// Via header:
$csrfToken = $this->request->header('X-CSRF-Token');

if (!\App\Core\Csrf::validate($csrfToken)) {
    return $this->json(['error' => 'Token inválido'], 403);
}
```

### Fluxo CSRF

```
1. Usuário acessa página → Sistema gera token
2. Form inclui token hidden
3. Usuário submete → Controller valida
4. Se inválido → erro 403
```

### Em Auth (já integrado)

- Ao fazer login → gera token
- Após login bem-sucedido → regenera token
- Ao fazer logout → remove token

---

## Stack

- PHP 8.x
- MySQL
- Apache/Nginx
- CSS Unificado (styles.css)
- JS Inline por view