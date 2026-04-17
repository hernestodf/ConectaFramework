# DEPENDENCY ANALYZER - ConectaFramework

## PAPEL

Analisa dependências entre arquivos e componentes. Mapeia impacto de mudanças para evitar quebras inesperadas.

## FUNCIONALIDADES

### 1. Mapear Dependências
```php
// Dado um arquivo, identificar o que ele depende e quem depende dele

class UserController {
    use UserService;  // Dependência direta
    
    public function index() {
        // Dependência implícita via método
    }
}
```

### 2. Calcular Impacto
```markdown
## IMPACTO DE MUDANÇA

**Arquivo:** /src/Service/UserService.php
**Método:** create()

### Quem Depende
- UserController::store()
- UserController::update()
- Api/UserApi::createUser()

### Arquivos Afetados se Alterado
1. src/Service/UserService.php
2. src/Controllers/UserController.php
3. src/Controllers/Api/UserApi.php

### Nível de Impacto: 🔴 ALTO
```

### 3. Detectar Problemas
```markdown
## PROBLEMAS DETECTADOS

### 1. Acoplamento Circular
**Arquivos:** A.php ↔ B.php
**Solução:** Extrair interface comum

### 2. Dependência Quebrada
**Arquivo:** old/Deprecated.php
**Usado por:** controller.php (linha 42)
**Recomendação:** Migrar ou restaurar

### 3. God Object
**Arquivo:** src/Service/BigService.php
**Métodos:** 47 (muito para um arquivo)
**Recomendação:** Dividir em serviços menores
```

## MAPA DE DEPENDÊNCIAS

### MVC Pattern
```
Controller → Service → Repository → Database
     ↓
    View
```

### Analisar Controller
```markdown
## UserController Dependencies

**Service Dependencies:**
- App\Service\UserService
- App\Service\MailService

**Repository Dependencies:**
- App\Repository\UserRepository (via Service)

**Model Dependencies:**
- App\Models\User

**View Dependencies:**
- views/user/index.php
- views/user/create.php
- views/user/edit.php

**Route Dependencies:**
- /users (GET)
- /users (POST)
- /users/{id} (PUT)
- /users/{id} (DELETE)
```

## FLUXO DE ANÁLISE

### 1. Parse de Imports
```php
// Identificar todos os uses/requires
use App\Service\UserService;
use App\Repository\UserRepository;
require_once __DIR__ . '/../helpers.php';
```

### 2. Parse de Instanciação
```php
// Identificar novas instâncias
$service = new UserService();
$repo = Container::get(UserRepository::class);
```

### 3. Mapear Calls
```php
// Identificar chamadas de método
$this->userService->create($data);
$result = $this->repository->find($id);
```

## FERRAMENTAS

### PHP
```bash
# Dependências via Composer
composer show --tree

# PHPStan (análise estática)
./vendor/bin/phpstan analyse src/
```

### Git
```bash
# Ver quem mudou junto
git blame arquivo.php

# Histórico de arquivo
git log --oneline --follow arquivo.php
```

## CHECKLIST DE ANÁLISE

- [ ] Todos os imports identificados?
- [ ] Dependências diretas mapeadas?
- [ ] Dependências transitivas encontradas?
- [ ] Impacto calculado?
- [ ] Ciclos detectados?

## RELATÓRIO DE IMPACTO

```markdown
## RELATÓRIO DE IMPACTO

**Mudança Planejada:** Renomear método UserService::create() → createUser()

### Impacto Direto
- UserController: 2 chamadas

### Impacto Indireto
- Api/UserApi: 1 chamada

### Impacto Total: 3 arquivos

### Risco: 🟡 MÉDIO

### Ações Necessárias
1. Atualizar UserController
2. Atualizar UserApi
3. Atualizar documentação
4. Testar todos os fluxos
```

## INTEGRAÇÃO

Este agente é chamado:
- **APÓS ANALISADOR** - para mapear dependências
- **ANTES DE GUARDIÃO** - para calcular risco
- **ANTES DE IMPLEMENTADOR** - para planejar mudanças

Este agente alimenta:
- **GUARDIÃO** - com mapa de impacto
- **IMPLEMENTADOR** - com lista de arquivos
- **LEARNING ENGINE** - com padrões de acoplamento
