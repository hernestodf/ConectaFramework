# Best Practices - ConectaFramework

## Boas Práticas

Este arquivo registra as boas práticas recomendadas para o desenvolvimento com o ConectaFramework.

---

## Backend - PHP

### Namespaces

| Componente | Namespace Correto |
|-----------|----------------|
| Controllers | App\\Controllers |
| Services | App\\Service |
| Repositories | App\\Repository |
| Middleware | App\\Http\\Middleware |
| Core | App\\Core |

### Estrutura de Services

```php
<?php
namespace App\Service;

use App\Repository\NomeRepository;

class NomeService extends BaseService
{
    protected string $entityName = 'Nome';

    public function __construct(?NomeRepository $repository = null)
    {
        parent::__construct($repository ?? new NomeRepository());
    }

    public function create(array $data): int
    {
        $this->validateRequired($data, ['name']);
        $data = $this->sanitize($data);
        $data['status'] = $data['status'] ?? 1;
        $id = $this->repository->create($data);
        $this->log('info', "Criado ID {$id}", $data);
        return $id;
    }
}
```

### Estrutura de Repositories

```php
<?php
namespace App\Repository;

class NomeRepository extends BaseRepository
{
    protected string $table = 'nomes';
    protected array $fillable = ['name', 'status'];
}
```

### Estrutura de Controllers

```php
<?php
namespace App\Controllers;

use App\Http\Controller;
use App\Core\Response;
use App\Service\NomeService;

class NomeController extends Controller
{
    private NomeService $service;

    public function __construct($request)
    {
        parent::__construct($request);
        $this->service = new NomeService();
    }

    public function index(): Response
    {
        $page = (int) $this->get('page', 1);
        $data = $this->service->paginate($page, 15);
        return $this->view('nome/index', [
            'title' => 'Nomes',
            'items' => $data['data'],
            'pagination' => $data['pagination'],
        ]);
    }
}
```

---

## Frontend - HTML/PHP

### Estrutura de Views

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
  <div class="tb-right">
    <a href="<?= $baseUrl ?>/modulo/create" class="btn btn-cyan">+ Novo</a>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <!-- CONTEÚDO AQUI -->
  </div>
</div>

<?php require dirname(__DIR__) . '/layout/footer.php'; ?>
```

### Formulários

```php
<form method="POST" action="<?= $baseUrl ?>/modulo/store">
  <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">

  <div class="fg">
    <div class="fl">Nome</div>
    <input type="text" name="name" class="fi" required>
  </div>

  <div class="flex-row-gap" style="margin-top:16px">
    <a href="<?= $baseUrl ?>/modulo" class="btn btn-red">Cancelar</a>
    <button type="submit" class="btn btn-cyan">Salvar</button>
  </div>
</form>
```

### Tabelas

```php
<table class="table-default">
  <thead>
    <tr>
      <th>ID</th><th>Nome</th><th>Status</th><th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $item): ?>
    <tr>
      <td><?= $item['id'] ?></td>
      <td><?= htmlspecialchars($item['name']) ?></td>
      <td><span class="badge <?= $item['status'] ? 'green' : 'red' ?>"><?= $item['status'] ? 'Ativo' : 'Inativo' ?></span></td>
      <td>
        <a href="<?= $baseUrl ?>/modulo/edit/<?= $item['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
```

---

## CSS - Classes Obrigatórias

| Classe | Uso |
|--------|-----|
| .col2 | Grid de 2 colunas |
| .fg | Field group (margin-bottom:16px) |
| .fl | Field label (uppercase + bold) |
| .fi | Input / select / textarea |
| .flex-row-gap | Flexbox com gap 8px |
| .card, .card-head, .card-title, .card-body | Cards |
| .table-default | Tabela estilizada |
| .badge.green / .red / .cyan | Status badges |
| .alert.red / .green / .cyan | Alertas |

### Botões

| Classe | Quando usar |
|--------|------------|
| .btn.btn-cyan | Salvar, criar, editar |
| .btn.btn-red | Cancelar, excluir |
| .btn.btn-green | Confirmar |
| .btn.btn-blue | Visualizar |
| .btn.btn-outline-cyan | Secundário |
| .btn.btn-sm | Pequeno (tabelas) |

---

## Rotas

```php
$app->router()->group('/modulo', function($router) {
    $router->get('/', [NomeController::class, 'index']);
    $router->get('/create', [NomeController::class, 'create']);
    $router->post('/store', [NomeController::class, 'store']);
    $router->get('/edit/{id}', [NomeController::class, 'edit']);
    $router->post('/update/{id}', [NomeController::class, 'update']);
    $router->post('/delete/{id}', [NomeController::class, 'delete']);
}, [\App\Http\Middleware\AuthMiddleware::class]);
```

---

## Segurança

### CSRF Obrigatório

```php
<input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">
```

### Escapar Outputs

```php
<?= htmlspecialchars($variavel) ?>  // strings
<?= number_format($preco, 2, ',', '.') ?>  // preços
<?= (int)$id ?>  // inteiros
```

---

## Arquivos Ignorados

```
/vendor/
/.env
/storage/logs/*.log
*.log
.DS_Store
Thumbs.db
```

---

## Checklist Antes de Entregar

- [ ] Namespace correto (Controllers com 's')
- [ ] fillable com todos os campos
- [ ] Service estende BaseService
- [ ] Controller injeta Service
- [ ] Views incluem layout
- [ ] CSRF em todos os forms
- [ ] Rotas com {id}
- [ ] htmlspecialchars() em outputs
- [ ] Botões corretos
- [ ] Nenhum CSS inline