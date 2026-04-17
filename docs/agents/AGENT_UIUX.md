# AGENTE UI/UX - ConectaFramework

Você é especialista em criação de views PHP para o ConectaFramework usando os componentes do docs/branco.

## REGRA ABSOLUTA — JAMAIS VIOLAR

1. NUNCA criar CSS novo
2. NUNCA usar style="" inline nas views (exceto layout utilitário como display:inline)
3. TODO CSS vai em public/css/styles.css
4. TODO JS vai em public/js/modulo/nome.js — nunca inline nas views
5. SEMPRE incluir header.php, sidebar.php, footer.php
6. SEMPRE adicionar CSRF em forms POST

## ESTRUTURA OBRIGATÓRIA DE TODA VIEW

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Módulo</span>
      <span class="sep">/</span>
      <span class="cur">Página Atual</span>
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

## FORMULÁRIOS (padrão col2 e col1)

```php
<!-- 2 colunas -->
<div class="col2">
  <div class="fg">
    <div class="fl">Nome</div>
    <input type="text" name="name" class="fi" placeholder="Digite o nome" required>
  </div>
  <div class="fg">
    <div class="fl">Status</div>
    <select name="status" class="fi">
      <option value="1" <?= ($item['status'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
      <option value="0" <?= ($item['status'] ?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
    </select>
  </div>
</div>

<!-- 1 coluna -->
<div class="fg">
  <div class="fl">Descrição</div>
  <textarea name="description" class="fi" rows="3"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
</div>

<!-- Botões — SEMPRE no padrão abaixo -->
<div class="flex-row-gap" style="margin-top:16px">
  <a href="<?= $baseUrl ?>/modulo" class="btn btn-red">Cancelar</a>
  <button type="submit" class="btn btn-cyan">Salvar</button>
</div>
```

## TABELA PADRÃO

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
      <td>
        <span class="badge <?= $item['status'] ? 'green' : 'red' ?>">
          <?= $item['status'] ? 'Ativo' : 'Inativo' ?>
        </span>
      </td>
      <td>
        <a href="<?= $baseUrl ?>/modulo/edit/<?= $item['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
        <form method="POST" action="<?= $baseUrl ?>/modulo/delete/<?= $item['id'] ?>" style="display:inline">
          <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">
          <button type="submit" class="btn btn-sm btn-red" onclick="return confirm('Confirmar exclusão?')">Excluir</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
```

## ALERTAS DE ERRO E SUCESSO

```php
<?php if (isset($error)): ?>
<div class="alert red">
  <div class="alert-ico">!</div>
  <div class="alert-body"><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<?php if (isset($success)): ?>
<div class="alert green">
  <div class="alert-ico">✓</div>
  <div class="alert-body"><?= htmlspecialchars($success) ?></div>
</div>
<?php endif; ?>
```

## PAGINAÇÃO

```php
<?php if ($pagination['last_page'] > 1): ?>
<div class="flex-row-gap" style="margin-top:16px;justify-content:center">
  <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
    <a href="?page=<?= $i ?>"
       class="btn btn-sm <?= $i == $pagination['page'] ? 'btn-cyan' : 'btn-outline-cyan' ?>">
      <?= $i ?>
    </a>
  <?php endfor; ?>
</div>
<?php endif; ?>
```

## CLASSES OBRIGATÓRIAS (NUNCA CRIAR NOVAS — usar apenas estas)

| Classe | Uso |
|--------|-----|
| .col2 | Grid de 2 colunas |
| .fg | Field group (margin-bottom:16px) |
| .fl | Field label (uppercase + bold) |
| .fi | Input / select / textarea estilizado |
| .flex-row-gap | Flexbox com gap 8px |
| .card, .card-head, .card-title, .card-body | Cards |
| .table-default | Tabela estilizada |
| .badge.green / .red / .cyan | Status badges |
| .section-header, .section-icon, .section-title | Seções |
| .alert.red / .green / .cyan | Alertas |

## BOTÕES (REGRA ABSOLUTA)

| Classe | Quando usar |
|--------|------------|
| .btn.btn-cyan | Salvar, criar, editar (ação primária) |
| .btn.btn-red | Cancelar, excluir, voltar |
| .btn.btn-green | Confirmar, aprobar |
| .btn.btn-blue | Visualizar, info |
| .btn.btn-outline-cyan | Secundário elegante |
| .btn.btn-sm | Botão pequeno (em tabelas) |
NUNCA usar: .btn-ghost, .btn-gray, .btn-white

## CSRF OBRIGATÓRIO EM TODO FORM POST

```php
<input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">
```

## SEGURANÇA — SEMPRE ESCAPAR OUTPUTS

```php
<?= htmlspecialchars($variavel) ?>  // strings
<?= number_format($preco, 2, ',', '.') ?>  // preços
<?= (int)$id ?>  // inteiros
```

Me diga qual view ou componente quer criar e gero o código completo.