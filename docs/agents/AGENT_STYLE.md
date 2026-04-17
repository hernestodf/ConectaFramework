# CODE STYLE ENFORCER - ConectaFramework

## PAPEL

Garante padronização de código PHP, JS e CSS. Mantém consistência em todo o projeto.

## PADRÕES DO FRAMEWORK

### PHP

#### Nomenclatura
```php
// Classes: PascalCase
class UserController {}
class UserService {}

// Métodos: camelCase
public function getUserById() {}
public function createNewUser() {}

// Variáveis: camelCase
$userName = 'João';
$isActive = true;

// Constantes: UPPER_SNAKE_CASE
const MAX_RETRY = 3;
const DB_HOST = 'localhost';

// Privacidade: underscore prefix (opcional)
private $_internalCache;
protected $_parentReference;
```

#### Estrutura de Arquivo
```php
<?php
// 1. Namespace
namespace App\Controllers;

// 2. Imports
use App\Core\Controller;
use App\Service\UserService;

// 3. Docblock da classe
/**
 * UserController - Gerencia usuários
 */

// 4. Classe
class UserController extends Controller {
    // 5. Constantes
    const MAX_PER_PAGE = 20;
    
    // 6. Propriedades
    protected UserService $userService;
    
    // 7. Construtor
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    
    // 8. Métodos públicos
    public function index() {}
    
    // 9. Métodos privados
    private function validateInput() {}
}
```

#### Formatação
```php
// ✅ CORRETO
if ($condition) {
    doSomething();
} else {
    doOtherThing();
}

// ❌ ERRADO
if ($condition) { doSomething(); } else { doOtherThing(); }

// ✅ CORRETO
function longFunction(
    string $param1,
    int $param2,
    array $param3
) {
    // ...
}

// ✅ CORRETO - Espaçamento
$a = 1 + 2;
$users = $this->userService->getAll(['active' => 1]);
```

### JavaScript

```javascript
// Classes: PascalCase
class UserManager {}

// Funções: camelCase
function getUserData() {}

// Constantes: UPPER_SNAKE_CASE
const MAX_RETRY = 3;

// Variáveis: camelCase
let userName = 'João';
const isActive = true;

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
/* Usar classes do design system, não seletores complexos */

/* ✅ CORRETO */
.card {
    padding: 1rem;
}

.btn-primary {
    background: #0dcaf0;
}

/* ❌ ERRADO */
div.container > .row > .col-md-4 > .card > .card-body > p {
    padding: 20px;
}

/* ✅ ORDEM ALFABÉTICA */
.card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
}
```

## CHECKLIST DE ESTILO

### PHP
- [ ] Indentação: 4 espaços
- [ ] Linha em branco entre métodos
- [ ] Docblocks em métodos públicos
- [ ] Types declarados (PHP 8+)
- [ ] Sem variáveis não usadas
- [ ] Sem código comentado (exceto documentação)

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

### PHP CS Fixer
```bash
php-cs-fixer fix src/ --rules=@PSR12
```

### Prettier
```bash
npx prettier --write "**/*.js"
npx prettier --write "**/*.css"
```

## RELATÓRIO DE ESTILO

```markdown
## RELATÓRIO DE ESTILO

**Arquivos analisados:** 15
**Problemas encontrados:** 8

### Problemas
1. **UserController.php:45** - Indentação incorreta
2. **script.js:12** - var ao invés de const

### Status: ⚠️ REQUER CORREÇÃO
```

## INTEGRAÇÃO

Este agente é chamado:
- **DURANTE IMPLEMENTADOR** - para formatar código
- **ANTES DE VISUAL QA** - para garantir padrão

Este agente alimenta:
- **CRÍTICO** - com score de estilo
- **LEARNING ENGINE** - com padrões violados
