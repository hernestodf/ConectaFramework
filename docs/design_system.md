# Design System - ConectaFramework

## Visão Geral

Este documento define o Design System do ConectaFramework - todos os componentes visuais, classes CSS e padrões de UI/UX.

---

## Cores do Tema

### Cores Principais

| Cor | Hex | Uso |
|-----|-----|-----|
| Cyan (default) | #0B6E8C | Botões primários, links |
| Rosa | #E11D48 | Alertas, botões perigo |
| Azul | #1D4ED8 | Informações |
| Verde | #059669 | Sucesso |
| Roxo (dark) | #8B5CF6 | Tema escuro |

---

## Componentes Base

### 1. Formulários

| Classe | Descrição |
|--------|----------|
| .col2 | Grid de 2 colunas |
| .col1 | Grid de 1 coluna |
| .fg | Field group (margin-bottom:16px) |
| .fl | Field label (uppercase + bold + margin-bottom:4px) |
| .fi | Input/select/textarea estilizado |
| .fi:focus | Estado de foco |
| .fi:disabled | Estado desabilitado |

### 2. Inputs

```html
<!-- Input texto -->
<input type="text" name="nome" class="fi" placeholder="Digite...">

<!-- Select -->
<select name="status" class="fi">
  <option value="1">Ativo</option>
  <option value="0">Inativo</option>
</select>

<!-- Textarea -->
<textarea name="desc" class="fi" rows="3"></textarea>
```

### 3. Botões

| Classe | Uso |
|--------|-----|
| .btn | Botão base |
| .btn-cyan | Salvar, criar, editar |
| .btn-red | Cancelar, excluir |
| .btn-green | Confirmar |
| .btn-blue | Visualizar |
| .btn-outline-cyan | Secundário |
| .btn-sm | Pequeno (tabelas) |
| .btn-ghost | Transparente |
| .btn-gray | Cinza |

### 4. Estrutura de Layout

| Classe | Descrição |
|--------|----------|
| #topbar | Barra superior |
| .tb-left | Conteúdo esquerdo |
| .tb-right | Conteúdo direito |
| #main | Área principal |
| .content | Container de conteúdo |
| .sidebar | Menu lateral |
| .breadcrumb | Trilha de navegação |
| .sep | Separador (/) |
| .cur | Página atual |

### 5. Cards

| Classe | Descrição |
|--------|----------|
| .card | Card base |
| .card-head | Cabeçalho |
| .card-title | Título do card |
| .card-body | Corpo do card |

### 6. Tabelas

| Classe | Descrição |
|--------|----------|
| .table-default | Tabela estilizada |
| .table-striped | Listras alternadas |
| .table-hover | Highlight linha |

### 7. Badges

| Classe | Cor |
|--------|-----|
| .badge | Badge base |
| .badge-green | Verde (ativo) |
| .badge-red | Vermelho (inativo) |
| .badge-cyan | Cyan |
| .badge-gray | Cinza |

### 8. Alertas

| Classe | Cor |
|--------|-----|
| .alert | Alerta base |
| .alert-red | Erro |
| .alert-green | Sucesso |
| .alert-cyan | Info |
| .alert-ico | Ícone do alerta |

### 9. Utilitários

| Classe | Descrição |
|--------|----------|
| .flex-row-gap | Flexbox com gap 8px |
| .flex-center | Centralizar |
| .text-center | Texto centralizado |
| .text-right | Texto à direita |
| .flex-between | Espaço entre |
| .mt-1 a .mt-5 | Margin top |
| .mb-1 a .mb-5 | Margin bottom |
| .p-1 a .p-5 | Padding |
| .w-100 | Largura 100% |
| .hidden | Oculto |

---

## Hierarquia de Arquivos

```
public/
├── css/styles.css    → Todos os estilos
└── js/                → Scripts por módulo

views/
├── layout/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
└── modulo/
    ├── index.php
    ├── create.php
    └── edit.php
```

---

## Regras de Uso

### Regra 1: CSS
- NUNCA criar CSS inline (exceto display:inline, margin-top)
- Todas as classes em public/css/styles.css

### Regra 2: JS
- NUNCA criar JS inline nas views
- Scripts em public/js/modulo/nome.js

### Regra 3: Layout
- SEMPRE incluir header.php, sidebar.php, footer.php

### Regra 4: CSRF
- SEMPRE adicionar token em forms POST

### Regra 5: Escape
- SEMPRE usar htmlspecialchars() em outputs

---

## Exemplos

### Página de Lista

```php
<?php require 'layout/header.php'; ?>
<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Produtos</span><span class="sep">/</span><span class="cur">Lista</span>
    </div>
  </div>
  <div class="tb-right">
    <a href="/produtos/create" class="btn btn-cyan">+ Novo</a>
  </div>
</header>
<?php require 'layout/sidebar.php'; ?>
<div id="main">
  <div class="content">
    <div class="card">
      <div class="card-head">
        <span class="card-title">Lista</span>
      </div>
      <div class="card-body" style="padding:0">
        <table class="table-default">
          <thead>
            <tr><th>ID</th><th>Nome</th><th>Status</th><th>Ações</th></tr>
          </thead>
          <tbody>
            <?php foreach($items as $item): ?>
            <tr>
              <td><?= $item['id'] ?></td>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td>
                <span class="badge <?= $item['status'] ? 'green' : 'red' ?>">
                  <?= $item['status'] ? 'Ativo' : 'Inativo' ?>
                </span>
              </td>
              <td>
                <a href="/produtos/edit/<?= $item['id'] ?>" class="btn btn-sm btn-cyan">Editar</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require 'layout/footer.php'; ?>
```

### Página de Formulário

```php
<?php require 'layout/header.php'; ?>
<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Produtos</span><span class="sep">/</span><span class="cur">Novo</span>
    </div>
  </div>
</header>
<?php require 'layout/sidebar.php'; ?>
<div id="main">
  <div class="content">
    <div class="card">
      <div class="card-head">
        <span class="card-title">Novo Produto</span>
      </div>
      <div class="card-body">
        <form method="POST" action="/produtos/store">
          <input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">
          
          <div class="fg">
            <div class="fl">Nome</div>
            <input type="text" name="name" class="fi" required>
          </div>
          
          <div class="fg">
            <div class="fl">Status</div>
            <select name="status" class="fi">
              <option value="1">Ativo</option>
              <option value="0">Inativo</option>
            </select>
          </div>
          
          <div class="flex-row-gap" style="margin-top:16px">
            <a href="/produtos" class="btn btn-red">Cancelar</a>
            <button type="submit" class="btn btn-cyan">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php require 'layout/footer.php'; ?>
```

---

## Referência Rápida

| Precisa | Use |
|--------|-----|
| Formulário | .fi, .fl, .fg |
| Grid 2 colunas | .col2 |
| Botão salvar | .btn-cyan |
| Botão cancelar | .btn-red |
| Table listar | .table-default |
| Card info | .card |
| Status ativo | .badge-green |
| Status inativo | .badge-red |
| Alerta erro | .alert-red |
| Alerta sucesso | .alert-green |