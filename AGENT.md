# AGENT.md - Guia para Criar Novas Funcionalidades

Este documento define os padrões e regras do ConectaFramework. O agent DEVE seguir rigorosamente estes padrões ao criar novas funcionalidades.

---

## 1. VISÃO GERAL DO FRAMEWORK

### Arquitetura
```
URL Request → Controller → Service → Repository → Database
```

### Stack
- PHP 8.x (namespaces)
- MySQL
- Apache
- CSS único (styles.css)
- JS separado (arquivos)

---

## 2. ESTRUTURA DE ARQUIVOS (OBRIGATÓRIO)

```
novoframework/
├── src/
│   ├── Controllers/           → App\Controllers\NomeController
│   ├── Service/              → App\Service\NomeService (estende BaseService)
│   ├── Repository/           → App\Repository\NomeRepository (estende BaseRepository)
│   ├── Auth/                → App\Auth\Rbac
│   ├── Http/Middleware/      → App\Http\Middleware\NomeMiddleware
│   ├── Database/            → App\Database\Connection
│   └── Core/               → Application, Router, Request, Response, Logger, Debug
├── views/
│   ├── layout/              → header.php, sidebar.php, footer.php
│   ├── modulo/             → index.php, create.php, edit.php, show.php
│   └── errors/             → 404.php, 500.php
├── public/
│   ├── css/styles.css       → CSS ÚNICO (todo CSS aqui)
│   ├── js/               → JS separado por módulo
│   └── index.php          → Entry point
├── config/
│   └── app.php          → name, logo, theme (5 opções)
├── storage/
│   └── logs/            → logs diários
└── deploy/
    └── deploy.py       → FTP deploy
```

**NUNCA criar estruturas diferentes desta!**

---

## 3. CSS + JS (REGRAS OBRIGATÓRIAS)

### CSS - Arquivo Único
- **ARQUIVO:** `public/css/styles.css`
- **REGRA:** TODO CSS vai neste arquivo. NUNCA criar outro arquivo CSS.
- **NUNCA usar inline styles (`style=""`) nas views.**

### JS - Arquivos Separados
- **ARQUIVO:** `public/js/layout/*.js` (compartilhado) e `public/js/modulo/*.js` (específico)
- **REGRA:** JS vai em arquivos separados, não inline nas views.

---

## 4. CONFIGURAÇÕES (.env)

### Arquivo: .env
```env
APP_ENV=local                    # local | production
BASE_URL=http://localhost/novoframework/public

# DB Local (usado quando APP_ENV=local)
DB_LOCAL_HOST=localhost
DB_LOCAL_PORT=3306
DB_LOCAL_NAME=novoframework
DB_LOCAL_USER=root
DB_LOCAL_PASS=senha

# DB Online (usado quando APP_ENV=production)
DB_ONLINE_HOST=localhost
DB_ONLINE_PORT=3306
DB_ONLINE_NAME=novoframework
DB_ONLINE_USER=admin
DB_ONLINE_PASS=senha
```

**Regras:**
- `APP_ENV=local` → usa DB_LOCAL_*
- `APP_ENV=production` → usa DB_ONLINE_*
- O Router detecta subpasta automaticamente via BASE_URL

---

## 5. SISTEMA DE TEMAS

### Config: config/app.php
```php
'theme' => [
    'active' => 'pink',  // default | pink | blue | green | dark
    'themes' => [
        'default' => ['primary' => '#0B6E8C', ...],
        'pink' => ['primary' => '#E11D48', ...],
        'blue' => ['primary' => '#1D4ED8', ...],
        'green' => ['primary' => '#059669', ...],
        'dark' => ['primary' => '#8B5CF6', ...],
    ],
],
```

---

## 6. RBAC (4 roles)

| Role | Descrição |
|------|----------|
| guest | Visitante (não logado) |
| user | Usuário comum |
| manager | Gerente |
| admin | Administrador |

### Métodos
```php
Rbac::hasRole('admin');    // true/false
Rbac::isAdmin();        // true/false
Rbac::isGuest();       // true/false
Rbac::check('permissao'); // true/false
```

---

## 7. AUTH

| Rota | Método | Descrição |
|------|--------|----------|
| /auth/login | GET | Formulário de login |
| /auth/login | POST | Fazer login |
| /auth/logout | POST | Fazer logout |

**Session:** `$_SESSION['user_id']` e `$_SESSION['user_role']`

---

## 8. MIDDLEWARE

### Disponíveis
- `App\Http\Middleware\AuthMiddleware` - Protege rotas
- `App\Http\Middleware\RbacMiddleware` - Verifica role

### Uso
```php
$app->router()->group('/admin', function($router) {
    $router->get('/', [AdminController::class, 'index']);
}, [App\Http\Middleware\AuthMiddleware::class]);
```

---

## 9. CRIAÇÃO DE NOVO MÓDULO (PASSO A PASSO)

### PASSO 1: Criar Tabela no Banco
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

### PASSO 2: Criar Repository
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

### PASSO 3: Criar Service
**Arquivo:** `src/Service/ProductService.php`
```php
<?php

namespace App\Service;

use App\Repository\ProductRepository;

class ProductService extends BaseService
{
    protected string $entityName = 'Product';

    public function __construct(?ProductRepository $repository = null)
    {
        $repository = $repository ?? new ProductRepository();
        parent::__construct($repository);
    }

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

    public function delete(int $id): int
    {
        $this->findOrFail($id);
        $result = $this->repository->delete($id);
        $this->log('info', "Deletado ID {$id}");
        return $result;
    }

    public function activate(int $id): int
    {
        $this->findOrFail($id);
        $result = $this->repository->update($id, ['status' => 1]);
        $this->log('info', "Ativado ID {$id}");
        return $result;
    }

    public function deactivate(int $id): int
    {
        $this->findOrFail($id);
        $result = $this->repository->update($id, ['status' => 0]);
        $this->log('info', "Desativado ID {$id}");
        return $result;
    }

    protected function formatPrice($price): float
    {
        if (is_string($price)) {
            $price = str_replace(['R$', ',', ' '], '', $price);
        }
        return (float) $price;
    }

    protected function validateForCreate(array $data): void
    {
        $this->validateRequired($data, ['name']);
        
        if (isset($data['price']) && $data['price'] < 0) {
            throw new \Exception('Preço não pode ser negativo');
        }
    }

    protected function validateForUpdate(array $data): void
    {
        if (isset($data['price']) && $data['price'] < 0) {
            throw new \Exception('Preço não pode ser negativo');
        }
    }
}
```

### PASSO 4: Criar Controller
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

    public function create(): Response
    {
        return $this->view('product/create', [
            'title' => 'Novo Produto',
        ]);
    }

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

    public function delete($id): Response
    {
        try {
            $this->service->delete($id);
            return $this->redirect($this->baseUrl . '/products');
        } catch (\Exception $e) {
            return $this->redirect($this->baseUrl . '/products');
        }
    }
}
```

### PASSO 5: Criar Views
**Arquivo:** `views/product/index.php`
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

### PASSO 6: Registrar Rotas
**Arquivo:** `public/index.php`
```php
// ROTAS DE PRODUTOS (protegidas)
$app->router()->group('/products', function($router) {
    $router->get('/', [App\Controllers\ProductController::class, 'index']);
    $router->get('/create', [App\Controllers\ProductController::class, 'create']);
    $router->post('/store', [App\Controllers\ProductController::class, 'store']);
    $router->get('/edit/{id}', [App\Controllers\ProductController::class, 'edit']);
    $router->post('/update/{id}', [App\Controllers\ProductController::class, 'update']);
    $router->get('/show/{id}', [App\Controllers\ProductController::class, 'show']);
    $router->post('/delete/{id}', [App\Controllers\ProductController::class, 'delete']);
}, [App\Http\Middleware\AuthMiddleware::class]);
```

---

## 10. MÉTODOS DO CONTROLLER

| Método | Descrição |
|--------|----------|
| `$this->view('view', data, status)` | Renderizar view |
| `$this->json(data)` | Retornar JSON |
| `$this->redirect(url)` | Redirecionar |
| `$this->back()` | Voltar |
| `$this->input('key')` | GET ou POST |
| `$this->get('key')` | Apenas GET |
| `$this->post('key')` | Apenas POST |

---

## 11. DEBUG E LOGS

### Logger
- **Arquivo:** `storage/logs/Y-m-d.log`
- **Uso:** `$this->log('info', 'mensagem', $data)` (via Service)

### Error Handler
- **Arquivo:** `src/Core/ErrorHandler.php`
- **Local:** erro completo
- **production:** ID simples (ERR-xxxx)

---

## 12. DEPLOY

### FTP
```bash
python deploy/deploy.py
```

### Dump Banco
```bash
mysqldump -u root -p senha nome_banco > database/dump.sql
```

---

## 13. O QUE NÃO FAZER (ERROS COMUNS)

| ERRO | CORREÇÃO |
|------|----------|
| SQL no Controller | Usar Repository |
| Service sem BaseService | Estender BaseService |
| CSS inline | Colocar em styles.css |
| JS inline | Criar arquivo em public/js/ |
| Namespace errado | Usar `App\Controller\`, `App\Service\`, `App\Repository\` |
| View sem layout | Usar `require layout/header.php`, `sidebar.php`, `footer.php` |
| Esquecer rotas | Registrar em public/index.php |
| Esquecer .env | Criar com BASE_URL e DB_LOCAL_* |

---

## 14. BASESERVICE (HERDAR)

```php
namespace App\Service;

use App\Repository\BaseRepository;
use App\Core\Logger;

abstract class BaseService
{
    protected BaseRepository $repository;
    protected string $entityName = 'Entity';

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        $message = "{$this->entityName}: {$message}";
        Logger::$level($message, $context);
    }

    protected function findOrFail(int $id): ?array
    {
        $data = $this->repository->find($id);
        if (!$data) {
            throw new \Exception("{$this->entityName} não encontrado(a): {$id}");
        }
        return $data;
    }

    protected function validateRequired(array $data, array $fields): void
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \Exception("Campo obrigatório: {$field}");
            }
        }
    }

    protected function sanitize(array $data): array
    {
        return array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $data);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->paginate($page, $perPage);
    }
}
```

---

## 15. BASEREPOSITORY (JÁ EXISTS)

```php
namespace App\Repository;

use App\Database\Connection;
use PDO;

abstract class BaseRepository
{
    protected string $table;
    protected array $fillable = [];

    public function all(): array
    {
        return Connection::query("SELECT * FROM {$this->table}");
    }

    public function find(int $id): ?array
    {
        $results = Connection::query("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
        return $results[0] ?? null;
    }

    public function findBy(string $field, $value): ?array
    {
        $results = Connection::query("SELECT * FROM {$this->table} WHERE $field = ?", [$value]);
        return $results[0] ?? null;
    }

    public function create(array $data): int
    {
        $data = $this->fill($data);
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        
        Connection::exec("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)", array_values($data));
        
        return (int) Connection::lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $data = $this->fill($data);
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';
        
        return Connection::exec("UPDATE {$this->table} SET $sets WHERE id = ?", array_merge(array_values($data), [$id]));
    }

    public function delete(int $id): int
    {
        return Connection::exec("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        $results = Connection::query("SELECT * FROM {$this->table} LIMIT $perPage OFFSET $offset");
        $total = Connection::query("SELECT COUNT(*) as total FROM {$this->table}");
        
        return [
            'data' => $results,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total[0]['total'] ?? 0,
                'last_page' => ceil(($total[0]['total'] ?? 1) / $perPage),
            ]
        ];
    }

    protected function fill(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
---

## 16. COMO CRIAR API

O framework suporta WEB e API simultaneamente.

### WEB vs API

| Aspecto | WEB | API |
|---------|-----|-----|
| Resposta | HTML (view) | JSON (json) |
| Autenticação | Session | Token |
| Retorno | `$this->view()` | `$this->json()` |
| Rota típica | /products | /api/products |

### Exemplo de Rotas API

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

### Exemplo de Controller API

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

    // GET /api/products - Listar todos
    public function apiIndex(): Response
    {
        $products = $this->service->all();
        
        return $this->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // GET /api/products/{id} - Buscar um
    public function apiShow($id): Response
    {
        try {
            $product = $this->service->findOrFail($id);
            
            return $this->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Produto não encontrado'
            ], 404);
        }
    }

    // POST /api/products - Criar
    public function apiStore(): Response
    {
        try {
            $data = [
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'price' => $this->post('price'),
                'status' => (int) $this->post('status', 1),
            ];
            
            $id = $this->service->create($data);
            
            return $this->json([
                'success' => true,
                'data' => ['id' => $id]
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // PUT /api/products/{id} - Atualizar
    public function apiUpdate($id): Response
    {
        try {
            $data = [
                'name' => $this->post('name'),
                'description' => $this->post('description'),
                'price' => $this->post('price'),
                'status' => (int) $this->post('status', 1),
            ];
            
            $this->service->update($id, $data);
            
            return $this->json([
                'success' => true,
                'message' => 'Atualizado com sucesso'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    // DELETE /api/products/{id} - Deletar
    public function apiDelete($id): Response
    {
        try {
            $this->service->delete($id);
            
            return $this->json([
                'success' => true,
                'message' => 'Deletado com sucesso'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
```

### Códigos HTTP

| Código | Significado |
|--------|------------|
| 200 | OK |
| 201 | Criado |
| 400 | Erro na requisição |
| 401 | Não autorizado |
| 404 | Não encontrado |
| 500 | Erro interno |

### Métodos HTTP

| Método | Descrição |
|--------|----------|
| GET | Buscar/ler |
| POST | Criar |
| PUT | Atualizar |
| DELETE | Deletar |

---

## 17. PROTEÇÃO CSRF

O framework possui proteção CSRF nativa para防止 ataques.

### O que é CSRF

CSRF (Cross-Site Request Forgery) previne que atacantes enviem formulários em nome do usuário sem consentimento.

### Arquivo: src/Core/Csrf.php

```php
namespace App\Core;

class Csrf
{
    // Gerar novo token - chama ao fazer login
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    // Validar token
    public static function validate(string $token = null): bool
    {
        if ($token === null) {
            return false;
        }
        $sessionToken = $_SESSION['csrf_token'] ?? null;
        return $sessionToken !== null && $token === $sessionToken;
    }

    // Obter token atual
    public static function getToken(): ?string
    {
        return $_SESSION['csrf_token'] ?? null;
    }

    // Verificar se token existe
    public static function hasToken(): bool
    {
        return isset($_SESSION['csrf_token']);
    }

    // Regenerar token (após login)
    public static function regenerate(): string
    {
        return self::generate();
    }

    // Remover token (ao logout)
    public static function forget(): void
    {
        unset($_SESSION['csrf_token']);
    }
}
```

### Como Usar em Forms (WEB)

```php
// Em TODO form, adicionar campo hidden:
<form method="POST" action="/admin/salvar">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>"/>
    <!-- outros campos -->
</form>
```

### Como Validar em Controller

```php
// No método que recebe POST:
public function store(): Response
{
    $csrfToken = $this->post('_csrf_token');
    
    if (!\App\Core\Csrf::validate($csrfToken)) {
        return $this->json(['error' => 'Token inválido'], 403);
    }
    
    // Continuar com a lógica...
}
```

### Como Validar em API

```php
// No header da requisição:
$csrfToken = $this->request->header('X-CSRF-Token');

if (!\App\Core\Csrf::validate($csrfToken)) {
    return $this->json(['error' => 'Token inválido'], 403);
}
```

### Fluxo CSRF no Framework

```
1. Usuário acessa página (login ou qualquer página)
   ↓
2. Sistema gera token CSrf::generate() se não existir
   ↓
3. Form inclui token hidden: <input name="_csrf_token">
   ↓
4. Usuário submeteform
   ↓
5. Controller valida CSrf::validate(token)
   ↓
6. Se válido → processa | Se inválido → erro 403
```

### Em AuthController (login)

```php
public function login(): Response
{
    if (!Csrf::hasToken()) {
        Csrf::generate();
    }
    return $this->view('auth/login', [
        'title' => 'Login',
    ]);
}

public function doLogin(): Response
{
    $csrfInput = $this->post('_csrf_token');
    
    if (!Csrf::validate($csrfInput)) {
        return $this->json(['error' => 'Token inválido'], 403);
    }

    // Verificar credenciais...
    if (Rbac::login($email, $password)) {
        Csrf::regenerate();  // Novo token após login
        return $this->json(['success' => true]);
    }
}

public function logout(): Response
{
    Csrf::forget();  // Remover token ao logout
    Rbac::logout();
    return $this->redirect('/auth/login');
}
```

---

## 18. COMO USAR O TEMA EM PÁGINAS STANDALONE

Páginas standalone (que não usam layout compartilhado) precisam receber o tema dinamicamente.

### Exemplo: Login Page

**1. No Controller, passar o tema:**

```php
use App\Core\Application;

public function login(): Response
{
    if (!Csrf::hasToken()) {
        Csrf::generate();
    }
    
    $app = Application::getInstance();
    $theme = $app->getTheme();
    
    return $this->view('auth/login', [
        'title' => 'Login',
        'theme' => $theme,
    ]);
}
```

**2. Na View, usar variáveis dinâmicas:**

```php
<style>
  :root {
    --neon-cyan: <?= $theme['primary'] ?? '#0B6E8C' ?>;
    --neon-cyan-glow: <?= $theme['primary_glow'] ?? 'rgba(11,110,140,0.28)' ?>;
    --bg-darkest: <?= $theme['background'] ?? '#FDF2F8' ?>;
    --bg-surface: <?= $theme['surface'] ?? '#FCE7F3' ?>;
    --text-1: <?= $theme['text'] ?? '#1E1B4B' ?>;
    --text-2: <?= $theme['text'] ?? '#1E1B4B' ?>;
    --text-3: <?= $theme['text_light'] ?? '#4C1D4E' ?>;
  }
</style>
```

**ATENÇÃO:** NUNCA hardcodar cores no CSS. Sempre usar variáveis PHP do tema.

### Variáveis do Tema Disponíveis

| Variável | Descrição |
|----------|-----------|
| $theme['primary'] | Cor principal do tema |
| $theme['primary_glow'] | Cor com transparência |
| $theme['background'] | Cor de fundo |
| $theme['surface'] | Cor da superfície |
| $theme['text'] | Cor do texto |
| $theme['text_light'] | Cor do texto claro |

---

## 19. ROTAS E ROUTER (OBRIGATÓRIO)

### Parâmetros de Rota

O router usa sintaxe `{param}` para parâmetros dinâmicos:

```php
// Routes com parâmetros
$router->get('/products/edit/{id}', [ProductController::class, 'edit']);
$router->get('/products/show/{id}', [ProductController::class, 'show']);
$router->post('/products/update/{id}', [ProductController::class, 'update']);
$router->post('/products/delete/{id}', [ProductController::class, 'delete']);
$router->get('/api/products/{id}', [ProductController::class, 'apiShow']);
$router->put('/api/products/{id}', [ProductController::class, 'apiUpdate']);
$router->delete('/api/products/{id}', [ProductController::class, 'apiDelete']);
```

**Importante:** O Router automaticamente:
- Aceita ou não trailing slash (`/products` = `/products/`)
- Converte `{id}` para parâmetro passado ao método do controller

### Grupo de Rotas

```php
$app->router()->group('/products', function($router) {
    $router->get('/', [ProductController::class, 'index']);
    $router->get('/create', [ProductController::class, 'create']);
    $router->post('/store', [ProductController::class, 'store']);
    $router->get('/edit/{id}', [ProductController::class, 'edit']);
    $router->post('/update/{id}', [ProductController::class, 'update']);
    $router->get('/show/{id}', [ProductController::class, 'show']);
    $router->post('/delete/{id}', [ProductController::class, 'delete']);
});

// Grupo com middleware de autenticação
$app->router()->group('/admin', function($router) {
    $router->get('/', [AdminController::class, 'index']);
}, [\App\Http\Middleware\AuthMiddleware::class]);
```

### Debug de Rotas

Para listar todas as rotas registradas, adicione temporariamente:
```php
$app->router()->printRoutes();
```

---

## 20. UI E FORMULÁRIOS (OBRIGATÓRIO)

O docs em `/docs/branco/` contém TODOS os componentes de UI (34 componentes). **NUNCA criar novos styles** - sempre usar classes existentes.

Os arquivos principais são:
- `./docs/branco/branco.html` - Todos os componentes com examples
- `./docs/branco/styles.css` - Todas as classes CSS

### Estrutura de Formulários (igual /docs/branco/)

```html
<!-- Layout com 2 colunas -->
<div class="col2">
  <div class="fg">
    <div class="fl">Label do Campo</div>
    <input type="text" name="campo" class="fi" placeholder="..." required>
  </div>
  <div class="fg">
    <div class="fl">Outro Campo</div>
    <input type="number" name="outro" class="fi" required>
  </div>
</div>

<!-- Ou 1 coluna -->
<div class="fg">
  <div class="fl">Nome</div>
  <input type="text" name="name" class="fi" required>
</div>

<div class="fg">
  <div class="fl">Descrição</div>
  <textarea name="description" class="fi" rows="3"></textarea>
</div>

<!-- Botões - SEMPRE use botões coloridos, nunca ghost ou gray -->
<div class="flex-row-gap" style="margin-top:16px">
  <a href="/rota" class="btn btn-red">Cancelar</a>
  <button type="submit" class="btn btn-cyan">Salvar</button>
</div>
```

### Classes de UI (OBRIGATÓRIO usar estas classes, NÃO criar novas)

| Classe | Descrição |
|--------|-----------|
| `.col2` | Container de 2 colunas (grid) |
| `.fg` | Field group (margin-bottom:16px) |
| `.fl` | Field label (uppercase, small, bold) |
| `.fi` | Field input (estilizado, focus states) |
| `.flex-row-gap` | Flexbox com gap 8px |
| `.section-header` | Section com ícone + título |
| `.section-icon` | Ícone da seção |
| `.section-title` | Título principal |
| `.section-sub` | Subtítulo |
| `.divider` | Linha separadora |
| `.card` | Card |
| `.card-head` | Cabeçalho do card |
| `.card-body` | Corpo do card |
| `.alert` | Alerta (red, green, cyan) |
| `.alert-ico` | Ícone do alerta |
| `.alert-body` | Corpo do alerta |

### Botões (SEMPRE coloridos)

**Botões sólidos (filled):**
| Classe | Uso |
|--------|-----|
| `.btn.btn-cyan` | Primário (salvar, criar, editar) |
| `.btn.btn-red` | Cancelar, excluir, danger |
| `.btn.btn-green` | Sucesso, confirmar |
| `.btn.btn-blue` | Información adicional |
| `.btn.btn-purple` | Destaque especial |
| `.btn.btn-orange` | Aviso, atenção |

**Botões outline:**
| Classe | Uso |
|--------|-----|
| `.btn.btn-outline-cyan` | Secundário elegante |

**NUNCA usar:**
- `.btn.btn-ghost` (sem estilo)
- `.btn.btn-gray` (não existe)
- `.btn.btn-ghost-*` (evitar)

### Referência de UI - Componentes Disponíveis

O docs em `/docs/branco/` contém TODOS os componentes de UI (34 componentes). **NUNCA criar novos styles** - sempre usar classes existentes.

**UI Elements (23):**
- Dashboard, Accordions, Alerts, Avatars, Badges, Buttons, Cards, Carousel, Chips, Icons, List Items, Modals, Progress, Popovers, Ratings, Skeleton, Spinners, Stepper, Tables, Tabs, Timeline, Tooltips

**Forms (5):**
- Form Inputs (`.fi`, `.fl`, `.fg`, `.col2`), Checkbox & Radio, File Input, Validações, Date Time

**Extras (2):**
- Invoice, Calendar

Ao criar nova página, SEMPRE consultar `/docs/branco/` para copiar estrutura HTML e classes CSS corretas. Os arquivos principais são:
- `./docs/branco/branco.html` - Todos os componentes com examples
- `./docs/branco/styles.css` - Todas as classes de CSS

**NUNCA criar classes CSS novas** - sempre usar as classes existentes nos docs.

---

## 21. SISTEMA DE TEMAS

### Arquivo de Configuração

O sistema de temas está em `config/app.php`:

```php
'theme' => [
    'active' => 'green',  // Tema atual: default | pink | blue | green | dark
    
    'themes' => [
        'default' => [...],
        'pink' => [...],
        'blue' => [...],
        'green' => [...],
        'dark' => [...],
    ],
],
```

### Como Trocar o Tema

Basta mudar o valor de `'active'` em `config/app.php`:

```php
// Temas disponíveis:
'default'  // Cyan (Padrão) - #0B6E8C
'pink'     // Rosa Chamativo - #E11D48  
'blue'    // Blue Professional - #1D4ED8
'green'   // Green Nature - #059669
'dark'    // Dark Mode - #8B5CF6
```

### Estrutura de Cada Tema

Cada tema precisa ter estas propriedades:

```php
'nome_tema' => [
    'name' => 'Nome para Display',
    'primary' => '#corhex',           // Cor principal (botões, links)
    'primary_glow' => 'rgba(...)',    // Cor com transparência para glow/shadow
    'sidebar_bg' => '#corhex',         // Cor de fundo da sidebar
    'sidebar_hover' => 'rgba(...)',    // Cor do hover nos menus (recomendado: primary com 0.4 opacity)
    'sidebar_text' => 'rgba(...)',     // Cor do texto na sidebar
    'surface' => '#corhex',           // Cor de superfície (cards)
    'background' => '#corhex',          // Cor de fundo da página
    'text' => '#corhex',               // Cor principal do texto
    'text_light' => '#corhex',          // Cor secundária do texto
],
```

### Como o Tema Funciona

O tema é aplicado automaticamente através de CSS Variables no `header.php`:

1. **header.php** gera CSS Variables com `!important`:
   ```php
   :root {
     --neon-cyan: #059669 !important;   // primary do tema
     --neon-cyan-glow: rgba(...) !important;
     --bg-dark: #064E3B !important;     // sidebar_bg
     --bg-surface: #D1FAE5 !important;   // surface
     ...
   }
   ```

2. **styles.css** usa essas variáveis:
   ```css
   .btn-cyan { background: var(--neon-cyan); }
   .ni:hover { background: var(--bg-hover); }
   ```

3. **Elementos afetados pelo tema:**
   - Botões (`.btn-cyan`, `.btn-primary`)
   - Badges (`.badge-cyan`)
   - Ícones de seção (`.section-icon`)
   - Hover dos menus (`.ni:hover`)
   - Topbar e sidebar
   - Cores de fundo e texto

### Adicionar Novo Tema

Para adicionar um novo tema, edite `config/app.php`:

1. Adicione novo item em `'themes'`:
```php
'orange' => [
    'name' => 'Laranja Vibrante',
    'primary' => '#F97316',
    'primary_glow' => 'rgba(249,115,22,0.28)',
    'sidebar_bg' => '#1E1B18',
    'sidebar_hover' => 'rgba(249,115,22,0.4)',
    'sidebar_text' => 'rgba(255,255,255,0.75)',
    'surface' => '#FFEDD5',
    'background' => '#FFF7ED',
    'text' => '#1E1B18',
    'text_light' => '#F97316',
],
```

2. Mude o `'active'` para o novo tema:
```php
'active' => 'orange',
```

### Importante

- **sidebar_hover** deve usar a cor primary com ~40% de opacity para que o hover siga o tema
- O tema é aplicado automaticamente a TODOS os elementos que usam as CSS Variables
- Não é necessário modificar HTML ou JS ao trocar o tema

---

## 22. PROBLEMAS COMUNS E SOLUÇÕES

### Rota retorna 404

1. **Verificar se rota está registrada:**
   ```php
   // Add temporary debug
   $app->router()->printRoutes();
   ```

2. **Verificar método HTTP:** GET vs POST deve corresponder

3. **Verificar sintaxe de parâmetros:** Use `{id}` não `(:id)`

4. **Verificar middleware:** Grupo pode ter middleware que bloqueia

### Middleware não executa

O Router deve executar middleware nas rotas. Verificar que existe código no dispatch.

### Parâmetros não chegam ao controller

O Router converte named groups para array. O execute usa `array_values()` para converter para índices numéricos.

### Session/Auth não funciona

Verificar que Session::start() está sendo chamado no Application::boot().

### Formulários sem estilo

Verificar que está usando classes corretas: `.fi`, `.fl`, `.fg`, `.col2` - NÃO usar `.form-*`.

---

## 23. BUSCA AUTOMÁTICA CEP E CNPJ

O framework possui busca automática via APIs gratuitas que preenche os dados automaticamente ao digitar CEP ou CNPJ e perder o foco do campo.

### APIs Utilizadas

| Campo | API |URL | gratuita |
|------|-----|-----|---------|
| CEP | ViaCEP | `https://viacep.com.br/ws/{cep}/json/` | ✅ Sim |
| CNPJ | BrasilAPI | `https://brasilapi.com.br/api/cnpj/v1/{cnpj}` | ✅ Sim |

### Campos Necessários no Banco

A tabela users (ou outra tabela) precisa ter estas colunas:

```sql
ALTER TABLE users ADD COLUMN cnpj VARCHAR(20) AFTER cep;
ALTER TABLE users ADD COLUMN logradouro VARCHAR(200);
ALTER TABLE users ADD COLUMN numero VARCHAR(20);
ALTER TABLE users ADD COLUMN complemento VARCHAR(100);
ALTER TABLE users ADD COLUMN bairro VARCHAR(100);
ALTER TABLE users ADD COLUMN cidade VARCHAR(100);
ALTER TABLE users ADD COLUMN uf VARCHAR(2);
```

### Repositório (UserRepository)

Adicionar os novos campos no fillable:

```php
protected array $fillable = [
    'name', 'email', 'password', 'telefone', 'celular', 'cep',
    'cnpj', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf',
    'role', 'status'
];
```

### Service (UserService)

Os métodos `create()` e `update()` já processam os novos campos automaticamente via `$this->sanitize($data)`.

### Views - create.php e edit.php

**1. Adicionar campos de endereço após CEP:**

```php
<div class="col2">
    <div class="fg">
        <div class="fl">CEP</div>
        <input type="text" name="cep" id="cep" class="fi" data-mask="XXXXX-XXX" 
               value="<?= htmlspecialchars($data['cep'] ?? '') ?>" placeholder="XXXXX-XXX">
    </div>
    <div class="fg">
        <div class="fl">CNPJ</div>
        <input type="text" name="cnpj" id="cnpj" class="fi" data-mask="XX.XXX.XXX/XXXX-XX" 
               value="<?= htmlspecialchars($data['cnpj'] ?? '') ?>" placeholder="XX.XXX.XXX/XXXX-XX">
    </div>
</div>

<div class="fg">
    <div class="fl">Logradouro</div>
    <input type="text" name="logradouro" id="logradouro" class="fi" 
           value="<?= htmlspecialchars($data['logradouro'] ?? '') ?>" placeholder="Rua...">
</div>

<div class="col2">
    <div class="fg">
        <div class="fl">Número</div>
        <input type="text" name="numero" id="numero" class="fi" 
               value="<?= htmlspecialchars($data['numero'] ?? '') ?>" placeholder="S/N">
    </div>
    <div class="fg">
        <div class="fl">Complemento</div>
        <input type="text" name="complemento" id="complemento" class="fi" 
               value="<?= htmlspecialchars($data['complemento'] ?? '') ?>" placeholder="Apto, sala...">
    </div>
</div>

<div class="col2">
    <div class="fg">
        <div class="fl">Bairro</div>
        <input type="text" name="bairro" id="bairro" class="fi" 
               value="<?= htmlspecialchars($data['bairro'] ?? '') ?>" placeholder="Bairro">
    </div>
    <div class="fg">
        <div class="fl">Cidade</div>
        <input type="text" name="cidade" id="cidade" class="fi" 
               value="<?= htmlspecialchars($data['cidade'] ?? '') ?>" placeholder="Cidade">
    </div>
</div>

<div class="fg">
    <div class="fl">UF</div>
    <select name="uf" id="uf" class="fi">
        <option value="">Selecione</option>
        <option value="AC" <?= ($data['uf'] ?? '') == 'AC' ? 'selected' : '' ?>>AC</option>
        <option value="AL" <?= ($data['uf'] ?? '') == 'AL' ? 'selected' : '' ?>>AL</option>
        <option value="AP" <?= ($data['uf'] ?? '') == 'AP' ? 'selected' : '' ?>>AP</option>
        <option value="AM" <?= ($data['uf'] ?? '') == 'AM' ? 'selected' : '' ?>>AM</option>
        <option value="BA" <?= ($data['uf'] ?? '') == 'BA' ? 'selected' : '' ?>>BA</option>
        <option value="CE" <?= ($data['uf'] ?? '') == 'CE' ? 'selected' : '' ?>>CE</option>
        <option value="DF" <?= ($data['uf'] ?? '') == 'DF' ? 'selected' : '' ?>>DF</option>
        <option value="ES" <?= ($data['uf'] ?? '') == 'ES' ? 'selected' : '' ?>>ES</option>
        <option value="GO" <?= ($data['uf'] ?? '') == 'GO' ? 'selected' : '' ?>>GO</option>
        <option value="MA" <?= ($data['uf'] ?? '') == 'MA' ? 'selected' : '' ?>>MA</option>
        <option value="MT" <?= ($data['uf'] ?? '') == 'MT' ? 'selected' : '' ?>>MT</option>
        <option value="MS" <?= ($data['uf'] ?? '') == 'MS' ? 'selected' : '' ?>>MS</option>
        <option value="MG" <?= ($data['uf'] ?? '') == 'MG' ? 'selected' : '' ?>>MG</option>
        <option value="PA" <?= ($data['uf'] ?? '') == 'PA' ? 'selected' : '' ?>>PA</option>
        <option value="PB" <?= ($data['uf'] ?? '') == 'PB' ? 'selected' : '' ?>>PB</option>
        <option value="PR" <?= ($data['uf'] ?? '') == 'PR' ? 'selected' : '' ?>>PR</option>
        <option value="PE" <?= ($data['uf'] ?? '') == 'PE' ? 'selected' : '' ?>>PE</option>
        <option value="PI" <?= ($data['uf'] ?? '') == 'PI' ? 'selected' : '' ?>>PI</option>
        <option value="RJ" <?= ($data['uf'] ?? '') == 'RJ' ? 'selected' : '' ?>>RJ</option>
        <option value="RN" <?= ($data['uf'] ?? '') == 'RN' ? 'selected' : '' ?>>RN</option>
        <option value="RS" <?= ($data['uf'] ?? '') == 'RS' ? 'selected' : '' ?>>RS</option>
        <option value="RO" <?= ($data['uf'] ?? '') == 'RO' ? 'selected' : '' ?>>RO</option>
        <option value="RR" <?= ($data['uf'] ?? '') == 'RR' ? 'selected' : '' ?>>RR</option>
        <option value="SC" <?= ($data['uf'] ?? '') == 'SC' ? 'selected' : '' ?>>SC</option>
        <option value="SP" <?= ($data['uf'] ?? '') == 'SP' ? 'selected' : '' ?>>SP</option>
        <option value="SE" <?= ($data['uf'] ?? '') == 'SE' ? 'selected' : '' ?>>SE</option>
        <option value="TO" <?= ($data['uf'] ?? '') == 'TO' ? 'selected' : '' ?>>TO</option>
    </select>
</div>
```

**2. Adicionar JavaScript para busca automática:**

Adicionar antes do `</script>` closing (após máscaras):

```javascript
// Busca automática de CEP
document.getElementById('cep').addEventListener('blur', function(e) {
    var cep = e.target.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.erro) {
                    document.getElementById('logradouro').value = data.logradouro || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('uf').value = data.uf || '';
                }
            })
            .catch(function(err) { console.error('CEP não encontrado'); });
    }
});

// Busca automática de CNPJ (BrasilAPI)
document.getElementById('cnpj').addEventListener('blur', function(e) {
    var cnpj = e.target.value.replace(/\D/g, '');
    if (cnpj.length === 14) {
        fetch('https://brasilapi.com.br/api/cnpj/v1/' + cnpj)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.razao_social) {
                    // Se não tiver nome, usa razão social
                    var nomeInput = document.querySelector('input[name="nome"]');
                    if (!nomeInput.value) {
                        nomeInput.value = data.razao_social;
                    }
                    document.getElementById('telefone').value = data.ddd_telefone_1 || '';
                    document.getElementById('cep').value = data.cep || '';
                    document.getElementById('logradouro').value = data.logradouro || '';
                    document.getElementById('numero').value = data.numero || '';
                    document.getElementById('complemento').value = data.complemento || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    document.getElementById('cidade').value = data.municipio || '';
                    document.getElementById('uf').value = data.uf || '';
                }
            })
            .catch(function(err) { console.error('CNPJ não encontrado'); });
    }
});
```

### Fluxo de Funcionamento

```
1. Usuário digita CEP (ex: 01001000)
   ↓
2. Perde foco (evento blur)
   ↓
3. JS chama ViaCEP API
   ↓
4. Recebe: logradouro, bairro, cidade, uf
   ↓
5. Preenche campos automaticamente

---

1. Usuário digita CNPJ (ex: 12.345.678/0001-90)
   ↓
2. Perde foco (evento blur)
   ↓
3. JS chama BrasilAPI
   ↓
4. Recebe: razão_social, telefone, cep, endereço
   ↓
5. Preenche campos automaticamente
```

### Campos Retornados pelas APIs

**ViaCEP ( cep: "01001000" ):**

| Campo API | Campo DB |
|-----------|----------|
| logradouro | logradouro |
| bairro | bairro |
| localide | cidade |
| uf | uf |
| cep | cep |

**BrasilAPI ( cnpj: "12.345.678/0001-90" ):**

| Campo API | Campo DB |
|-----------|----------|
| razao_social | nome (se vazio) |
| ddd_telefone_1 | telefone |
| cep | cep |
| logradouro | logradouro |
| numero | numero |
| complemento | complemento |
| bairro | bairro |
| municipio | cidade |
| uf | uf |

### Importante

- **blur** = quando o usuário sai do campo (perde o foco)
- A busca só ocorre se o CEP tiver 8 dígitos ou CNPJ tiver 14 dígitos
- O campo CEP precisa ter ID="cep" e os campos de endereço precisam ter os IDs corretos
- O campo CNPJ precisa ter ID="cnpj"
- both APIs são gratuitas e não requerem API key

---

**FIM DO AGENT.md** - Siga rigorosamente estes padrões!