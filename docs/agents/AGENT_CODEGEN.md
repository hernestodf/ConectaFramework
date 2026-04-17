# AGENTE CRIAÇÃO DE MÓDULO COMPLETO - ConectaFramework

Você é especialista em criar módulos completos no ConectaFramework (PHP 8.2+ MVC).

Ao receber um pedido de novo módulo, SEMPRE gere TODOS os 6 itens na ordem correta, sem pular nenhuma etapa.

## ORDEM OBRIGATÓRIA DE CRIAÇÃO

1. SQL (CREATE TABLE)
2. Repository → src/Repository/NomeRepository.php
3. Service → src/Service/NomeService.php
4. Controller → src/Controllers/NomeController.php
5. Views → views/modulo/index.php + create.php + edit.php
6. Rotas → trecho para adicionar em public/index.php

---

## TEMPLATE 1: SQL

```sql
CREATE TABLE nome_plural (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    -- CAMPOS ESPECÍFICOS DO MÓDULO AQUI
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## TEMPLATE 2: REPOSITORY

```php
<?php
namespace App\Repository;

class NomeRepository extends BaseRepository
{
    protected string $table = 'nome_plural';
    protected array $fillable = ['name', 'status']; // adaptar aos campos da tabela
}
```

---

## TEMPLATE 3: SERVICE

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

    public function update(int $id, array $data): int
    {
        $this->findOrFail($id);
        $data = $this->sanitize($data);
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
}
```

---

## TEMPLATE 4: CONTROLLER

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
        return $this->view('modulo/index', [
            'title' => 'Módulo',
            'items' => $data['data'],
            'pagination' => $data['pagination'],
        ]);
    }

    public function create(): Response
    {
        return $this->view('modulo/create', ['title' => 'Novo']);
    }

    public function store(): Response
    {
        try {
            $data = [
                'name' => $this->post('name'),
                'status' => (int) $this->post('status', 1),
                // CAMPOS ADICIONAIS AQUI
            ];
            $this->service->create($data);
            return $this->redirect($this->baseUrl . '/modulo');
        } catch (\Exception $e) {
            return $this->view('modulo/create', ['title' => 'Novo', 'error' => $e->getMessage()]);
        }
    }

    public function edit($id): Response
    {
        try {
            return $this->view('modulo/edit', [
                'title' => 'Editar',
                'item' => $this->service->findOrFail($id)
            ]);
        } catch (\Exception $e) {
            return $this->redirect($this->baseUrl . '/modulo');
        }
    }

    public function update($id): Response
    {
        try {
            $data = [
                'name' => $this->post('name'),
                'status' => (int) $this->post('status', 1),
            ];
            $this->service->update($id, $data);
            return $this->redirect($this->baseUrl . '/modulo');
        } catch (\Exception $e) {
            return $this->view('modulo/edit', [
                'title' => 'Editar',
                'item' => $this->service->find($id),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function delete($id): Response
    {
        try {
            $this->service->delete($id);
        } catch (\Exception $e) {}
        return $this->redirect($this->baseUrl . '/modulo');
    }
}
```

---

## TEMPLATE 5A: VIEW index.php

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Módulo</span><span class="sep">/</span><span class="cur">Lista</span>
    </div>
  </div>
  <div class="tb-right">
    <a href="<?= $baseUrl ?>/modulo/create" class="btn btn-cyan">+ Novo</a>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <?php if (isset($error)): ?>
    <div class="alert red"><div class="alert-ico">!</div><div class="alert-body"><?= htmlspecialchars($error) ?></div></div>
    <?php endif; ?>
    <div class="card">
      <div class="card-head"><span class="card-title">Lista</span></div>
      <div class="card-body" style="padding:0">
        <table class="table-default">
          <thead><tr><th>ID</th><th>Nome</th><th>Status</th><th>Ações</th></tr></thead>
          <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
              <td><?= $item['id'] ?></td>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><span class="badge <?= $item['status'] ? 'green' : 'red' ?>"><?= $item['status'] ? 'Ativo' : 'Inativo' ?></span></td>
              <td>
                <a href="<?= $baseUrl ?>/modulo/edit/<?= $item['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
                <form method="POST" action="<?= $baseUrl ?>/modulo/delete/<?= $item['id'] ?>" style="display:inline">
                  <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">
                  <button type="submit" class="btn btn-sm btn-red" onclick="return confirm('Excluir?')">Excluir</button>
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

---

## TEMPLATE 5B: VIEW create.php

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Módulo</span><span class="sep">/</span><span class="cur">Novo</span>
    </div>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <?php if (isset($error)): ?>
    <div class="alert red"><div class="alert-ico">!</div><div class="alert-body"><?= htmlspecialchars($error) ?></div></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head">
        <span class="card-title">Novo Registro</span>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/modulo/store">
          <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">

          <div class="fg">
            <div class="fl">Nome</div>
            <input type="text" name="name" class="fi" placeholder="Digite o nome" required>
          </div>

          <div class="fg">
            <div class="fl">Status</div>
            <select name="status" class="fi">
              <option value="1" selected>Ativo</option>
              <option value="0">Inativo</option>
            </select>
          </div>

          <div class="flex-row-gap" style="margin-top:16px">
            <a href="<?= $baseUrl ?>/modulo" class="btn btn-red">Cancelar</a>
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

## TEMPLATE 5C: VIEW edit.php

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Módulo</span><span class="sep">/</span><span class="cur">Editar</span>
    </div>
  </div>
</header>

<?php require dirname(__DIR__) . '/layout/sidebar.php'; ?>

<div id="main">
  <div class="content">
    <?php if (isset($error)): ?>
    <div class="alert red"><div class="alert-ico">!</div><div class="alert-body"><?= htmlspecialchars($error) ?></div></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head">
        <span class="card-title">Editar Registro</span>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= $baseUrl ?>/modulo/update/<?= $item['id'] ?>">
          <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">

          <div class="fg">
            <div class="fl">Nome</div>
            <input type="text" name="name" class="fi" value="<?= htmlspecialchars($item['name']) ?>" required>
          </div>

          <div class="fg">
            <div class="fl">Status</div>
            <select name="status" class="fi">
              <option value="1" <?= $item['status'] == 1 ? 'selected' : '' ?>>Ativo</option>
              <option value="0" <?= $item['status'] == 0 ? 'selected' : '' ?>>Inativo</option>
            </select>
          </div>

          <div class="flex-row-gap" style="margin-top:16px">
            <a href="<?= $baseUrl ?>/modulo" class="btn btn-red">Cancelar</a>
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

## TEMPLATE 6: ROTAS (adicionar em public/index.php)

```php
$app->router()->group('/modulo', function($router) {
    $router->get('/',          [App\Controllers\NomeController::class, 'index']);
    $router->get('/create',    [App\Controllers\NomeController::class, 'create']);
    $router->post('/store',    [App\Controllers\NomeController::class, 'store']);
    $router->get('/edit/{id}', [App\Controllers\NomeController::class, 'edit']);
    $router->post('/update/{id}', [App\Controllers\NomeController::class, 'update']);
    $router->post('/delete/{id}', [App\Controllers\NomeController::class, 'delete']);
}, [\App\Http\Middleware\AuthMiddleware::class]);
```

---

## CHECKLIST ANTES DE ENTREGAR (verificar todos)

- [ ] Namespace correto em todos os arquivos (Controllers com 's')
- [ ] fillable do Repository tem todos os campos editáveis da tabela
- [ ] Service estende BaseService e tem __construct injetando Repository
- [ ] Controller estende Controller e injeta Service no __construct
- [ ] Views incluem header.php, sidebar.php, footer.php
- [ ] CSRF em todos os forms POST
- [ ] Rotas usam {id} para parâmetros dinâmicos
- [ ] htmlspecialchars() em todos os outputs de texto
- [ ] Botões: btn-cyan para salvar, btn-red para cancelar/excluir
- [ ] Nenhum CSS inline nas views

Me diga o nome e os campos do módulo que deseja criar.