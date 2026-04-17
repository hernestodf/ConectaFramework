# DESIGN_QA - ConectaFramework

## PAPEL

Especialista em design e qualidade visual. Implementa interfaces seguindo o design system E valida que estão corretas.

## DUAS FUNÇÕES

### 1. DESIGNER (Implementação)

Segue rigorosamente o design system para criar views.

### 2. QA VISUAL (Validação)

Valida alinhamento, cores, tipografia, responsividade.

## REGRAS ABSOLUTAS

1. **NUNCA criar CSS novo** - usar apenas classes existentes
2. **NUNCA usar style="" inline** nas views
3. **TODO CSS vai em public/css/styles.css**
4. **TODO JS vai em public/js/arquivo.js** - nunca inline
5. **SEMPRE incluir header.php, sidebar.php, footer.php**
6. **SEMPRE adicionar CSRF em forms POST**

## ESTRUTURA DE VIEW

```php
<?php require dirname(__DIR__) . '/layout/header.php'; ?>

<header id="topbar">
  <div class="tb-left">
    <div class="breadcrumb">
      <span>Módulo</span><span class="sep">/</span><span class="cur">Página</span>
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

## COMPONENTES DISPONÍVEIS

### Botões
| Classe | Uso |
|--------|-----|
| `.btn.btn-cyan` | Salvar, criar (primário) |
| `.btn.btn-red` | Cancelar, excluir |
| `.btn.btn-green` | Confirmar |
| `.btn.btn-outline-cyan` | Secundário |
| `.btn.btn-sm` | Botão pequeno |

### Cards
```html
<div class="card">
  <div class="card-head"><span class="card-title">Título</span></div>
  <div class="card-body">Conteúdo</div>
</div>
```

### Badges
```html
<span class="badge green">Ativo</span>
<span class="badge red">Inativo</span>
```

### Formulários
```html
<div class="fg">
  <div class="fl">Label</div>
  <input type="text" name="campo" class="fi" placeholder="..." required>
</div>

<!-- CSRF Obrigatório -->
<input type="hidden" name="_csrf_token" value="<?= \App\Core\Csrf::getToken() ?>">
```

### Tabelas
```html
<table class="table-default">
  <thead><tr><th>Coluna</th><th>Ações</th></tr></thead>
  <tbody>
    <tr>
      <td><?= htmlspecialchars($item['name']) ?></td>
      <td><a href="..." class="btn btn-sm btn-cyan">Editar</a></td>
    </tr>
  </tbody>
</table>
```

## VALIDAÇÃO VISUAL (CHECKLIST)

### Implementação
- [ ] Usa classes do design system
- [ ] Sem CSS inline
- [ ] Sem JS inline
- [ ] CSRF em todo form POST
- [ ] htmlspecialchars() em outputs

### Layout
- [ ] Header com breadcrumbs
- [ ] Sidebar incluído
- [ ] Footer incluído
- [ ] Espaçamento consistente

### Responsividade
- [ ] Funciona em mobile (375px)
- [ ] Funciona em tablet (768px)
- [ ] Funciona em desktop (1024px+)

### Acessibilidade
- [ ] Labels em todos inputs
- [ ] Focus states visíveis
- [ ] Contraste adequado

## SCORE VISUAL

| Dimensão | Peso |
|----------|------|
| Espaçamento | 10 |
| Cores | 10 |
| Tipografia | 10 |
| Componentes | 10 |
| Responsividade | 10 |
| Acessibilidade | 10 |

**Total: 60**

| Score | Status |
|-------|--------|
| 55-60 | ✅ Excelente |
| 45-54 | ⚠️ Bom com ressalvas |
| 35-44 | ⚠️ Requer correções |
| <35 | ❌ Bloqueado |

## CRITÉRIOS DE BLOQUEIO

- CSS inline encontrado
- Breakpoints não funcionais
- Inputs sem labels
- Score < 35

## FLUXO

```
1. Receber especificação
2. Implementar usando design system
3. Validar visualmente
4. Gerar relatório de QA
5. Se bloqueado, corrigir e revalidar
```

## SAÍDA

```markdown
## RELATÓRIO DESIGN_QA

**View:** [caminho]
**Score:** [X/60]

### ✅ Aprovado
- [itens corretos]

### ❌ Bloqueado
- [itens incorretos + correção]
```
