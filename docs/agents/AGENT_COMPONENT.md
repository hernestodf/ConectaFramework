# COMPONENT ENGINEER - ConectaFramework

## PAPEL

Especialista em componentização. Transforma código repetido em componentes reutilizáveis seguindo o design system.

## PRINCÍPIOS

1. **DRY** - Don't Repeat Yourself
2. **Single Responsibility** - cada componente faz uma coisa
3. **Composability** - componentes combináveis
4. **Consistency** - seguir design system

## COMPONENTES DO FRAMEWORK

### Botões
```html
<!-- Usar classes do design system -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-outline">Outline</button>
```

### Cards
```html
<div class="card">
    <div class="card-header">Título</div>
    <div class="card-body">
        <p>Conteúdo</p>
    </div>
    <div class="card-footer">Footer</div>
</div>
```

### Formulários
```html
<div class="mb-3">
    <label class="form-label">Nome</label>
    <input type="text" class="form-control" name="name">
    <div class="form-text">Ajuda</div>
</div>
```

### Badges
```html
<span class="badge badge-success">Ativo</span>
<span class="badge badge-warning">Pendente</span>
<span class="badge badge-danger">Erro</span>
```

### Tabelas
```html
<table class="table table-striped">
    <thead>
        <tr>
            <th>Coluna 1</th>
            <th>Coluna 2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Dado 1</td>
            <td>Dado 2</td>
        </tr>
    </tbody>
</table>
```

### Alerts
```html
<div class="alert alert-success">Sucesso!</div>
<div class="alert alert-danger">Erro!</div>
<div class="alert alert-warning">Atenção!</div>
<div class="alert alert-info">Info</div>
```

## PADRÃO DE COMPONENTIZAÇÃO

### 1. Identificar Repetição
```php
// ❌ REPETIDO em 5 lugares
<div class="card">
    <div class="card-header bg-cyan text-white">
        <h5>Título</h5>
    </div>
    <div class="card-body">
        Conteúdo...
    </div>
</div>

// ✅ Criar partial view: /views/components/card.php
```

### 2. Criar Partial
```php
<?php
// /views/components/card.php
// Variáveis: $title, $content, $footer, $headerClass
?>
<div class="card">
    <?php if (isset($title)): ?>
    <div class="card-header <?= $headerClass ?? '' ?>">
        <h5><?= htmlspecialchars($title) ?></h5>
    </div>
    <?php endif; ?>
    <div class="card-body">
        <?= $content ?>
    </div>
    <?php if (isset($footer)): ?>
    <div class="card-footer">
        <?= $footer ?>
    </div>
    <?php endif; ?>
</div>
```

### 3. Usar em Views
```php
<?php
$title = 'Meu Card';
$content = '<p>Conteúdo aqui</p>';
$footer = '<button class="btn btn-primary">Ação</button>';
require __DIR__ . '/components/card.php';
?>
```

## REFATORAÇÃO DE CSS

### ❌ CSS Inline
```html
<div style="padding: 20px; margin: 10px; background: #f0f0f0;">
```

### ✅ Classes do Design System
```html
<div class="p-3 m-2 bg-light">
```

## CHECKLIST DE COMPONENTIZAÇÃO

- [ ] Código duplicado identificado?
- [ ] Componente extraído para partial?
- [ ] Nomes de classes consistentes?
- [ ] Segue design system?
- [ ] Sem CSS inline?
- [ ] Sem hardcoded styles?

## RELATÓRIO

```markdown
## COMPONENTIZAÇÃO APLICADA

### Novos Components
1. **card.php** - Card genérico
   - Replaces: 5 lugares

2. **form-group.php** - Campo de formulário
   - Replaces: 12 lugares

### Total de Duplicação Reduzida: 40%
```

## INTEGRAÇÃO

Este agente é chamado:
- **DURANTE IMPLEMENTADOR** - para componentizar durante criação
- **ANTES DE VISUAL QA** - para garantir consistência

Este agente alimenta:
- **VISUAL QA** - com componentes padronizados
- **LEARNING ENGINE** - com componentes criados
