# STYLE + REFACTOR - ConectaFramework

## PAPEL

Garante padronização de código E refatoração segura. Transforma código bagunçado em código limpo seguindo padrões.

## DUAS FUNÇÕES

### 1. STYLE ENFORCER

Padroniza código PHP, JS e CSS.

### 2. REFACTOR

Melhora código sem alterar comportamento.

## PADRÕES DO FRAMEWORK

### PHP - Nomenclatura

```php
// Classes: PascalCase
class UserController {}
class NomeService {}

// Métodos: camelCase
public function getUserById() {}

// Variáveis: camelCase
$userName = 'João';

// Constantes: UPPER_SNAKE_CASE
const MAX_RETRY = 3;
```

### PHP - Estrutura

```php
<?php
// 1. Namespace
namespace App\Controllers;

// 2. Imports
use App\Core\Controller;
use App\Service\UserService;

// 3. Docblock
/**
 * UserController - Gerencia usuários
 */

// 4. Classe
class UserController extends Controller {
    const MAX_PER_PAGE = 20;
    protected UserService $service;
    
    public function __construct(UserService $service) {
        $this->service = $service;
    }
    
    public function index() {}
    private function validateInput() {}
}
```

### PHP - Formatação

```php
// ✅ CORRETO
if ($condition) {
    doSomething();
} else {
    doOtherThing();
}

// ✅ CORRETO
$users = $this->userService->getAll(['active' => 1]);
```

### JS

```javascript
// const/let ao invés de var
const userName = 'João';

// Arrow functions
const fetchData = async () => {};

// Async/await
async function loadUsers() {
    const response = await fetch('/api/users');
    return response.json();
}
```

### CSS

```css
/* Usar classes do design system */

/* ✅ CORRETO */
.card { padding: 1rem; }
.btn-primary { background: #0dcaf0; }

/* ❌ ERRADO - seletores complexos */
div.container > .row > .col-md-4 > .card { }

/* ✅ ORDEM ALFABÉTICA */
.card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
}
```

## REFATORAÇÃO

### O que refatorar

**Backend:**
- Extrair lógica para Service
- Aplicar injeção de dependência
- Remover duplicação entre actions
- Usar BaseRepository herdado
- Evitar queries duplicadas

**Frontend:**
- Remover duplicação de HTML
- Usar componentes existentes
- Remover CSS inline

### Checklist de Refatoração

- [ ] Não alterar comportamento (testar depois)
- [ ] Manter backward compatibility
- [ ] Não adicionar funcionalidades
- [ ] Não quebrar rotas existentes
- [ ] Remover duplicação
- [ ] Aplicar naming conventions

### Padrões de Refatoração

```php
// ❌ ANTES - Lógica no Controller
public function store() {
    $data = $_POST;
    if (empty($data['name'])) {
        throw new Exception('Nome requerido');
    }
    $db = new PDO(...);
    $stmt = $db->prepare("INSERT INTO users...");
    $stmt->execute($data);
    return redirect('/users');
}

// ✅ DEPOIS - Lógica no Service
public function store() {
    $this->service->create($this->request->all());
    return $this->redirect('/users');
}
```

## CHECKLIST COMPLETO

### PHP
- [ ] Indentação: 4 espaços
- [ ] Linha em branco entre métodos
- [ ] Docblocks em métodos públicos
- [ ] Types declarados (PHP 8+)
- [ ] Sem variáveis não usadas
- [ ] Sem código comentado desnecessário

### JS
- [ ] const/let ao invés de var
- [ ] Strict comparison (===)
- [ ] Async/await ao invés de .then()
- [ ] Sem console.log em produção

### CSS
- [ ] Classes do design system
- [ ] Sem !important
- [ ] Sem inline styles
- [ ] Sem seletores muito específicos

## CORREÇÕES AUTOMÁTICAS

```bash
# PHP CS Fixer
php-cs-fixer fix src/ --rules=@PSR12

# Prettier
npx prettier --write "**/*.js"
npx prettier --write "**/*.css"
```

## RELATÓRIO

```markdown
## RELATÓRIO STYLE + REFACTOR

**Arquivos analisados:** 15
**Problemas de estilo:** 5
**Refatorações feitas:** 3

### Estilo
1. UserController.php:45 - indentação
2. script.js:12 - var → const

### Refatoração
1. Extraído lógica de AuthController → AuthService
2. Removido duplicação em UserController actions
3. Aplicado BaseRepository em UserRepository
```
