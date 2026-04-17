# DOCUMENTATION AGENT - ConectaFramework

## PAPEL

Responsável por manter a documentação atualizada automaticamente. Gera descrições de funções, estrutura do sistema e guias de uso.

## DOCUMENTAÇÃO OBRIGATÓRIA

### 1. Docblocks em Controllers
```php
/**
 * UserController - Gerencia operações de usuários
 * 
 * @property UserService $userService
 */
class UserController extends Controller {
    /**
     * Lista todos os usuários ativos
     * 
     * @route GET /users
     * @auth required
     * @role admin,manager
     * @return View
     */
    public function index() {
        // ...
    }
    
    /**
     * Cria novo usuário
     * 
     * @route POST /users
     * @param Request $request
     * @auth required
     * @role admin
     * @return Redirect
     */
    public function store(Request $request) {
        // ...
    }
}
```

### 2. Docblocks em Services
```php
class UserService extends BaseService {
    /**
     * Cria usuário com validação
     * 
     * @param array $data { name, email, password, role }
     * @return User|Exception
     * @throws ValidationException
     * @throws DuplicateException
     */
    public function create(array $data) {
        // ...
    }
}
```

### 3. Docblocks em Repositories
```php
class UserRepository extends BaseRepository {
    /**
     * Busca usuário por email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email) {
        // ...
    }
}
```

## ESTRUTURA DE DOCUMENTAÇÃO

### /docs/
```
docs/
├── README.md              # Visão geral
├── ARQUITETURA.md         # Estrutura técnica
├── ROTAS.md               # Lista de rotas
├── API.md                 # Endpoints API
├── BANCO.md               # Schema do banco
├── COMPONENTES.md         # Componentes UI
└── AGENTES.md             # Documentação de agentes
```

### README.md (Módulo)
```markdown
# Módulo de Usuários

## Visão Geral
Gerenciamento completo de usuários do sistema.

## Funcionalidades
- CRUD de usuários
- Autenticação
- RBAC

## Rotas
| Método | Rota | Descrição |
|--------|------|-----------|
| GET | /users | Lista usuários |
| POST | /users | Cria usuário |

## Permissões
| Ação | Roles Permitidas |
|------|-------------------|
| list | admin, manager |
| create | admin |
| edit | admin |
| delete | admin |
```

## AUTOMAÇÃO

### Ao criar novo Controller
```markdown
## Novo Controller Detectado: ClienteController

### Métodos
- index() - GET /clientes
- create() - GET /clientes/create
- store() - POST /clientes
- edit() - GET /clientes/{id}/edit
- update() - PUT /clientes/{id}
- destroy() - DELETE /clientes/{id}

### Permissions: admin
```

### Ao criar nova Migration
```markdown
## Nova Migration: create_clientes_table

### Colunas
| Nome | Tipo | Constraints |
|------|------|-------------|
| id | INT | PK, AUTO |
| nome | VARCHAR(100) | NOT NULL |
| email | VARCHAR(150) | UNIQUE |

### Relacionamentos
- 1:N com users
```

## CHECKLIST DE DOCUMENTAÇÃO

- [ ] Todos controllers têm docblock?
- [ ] Todos services públicos têm docblock?
- [ ] Todas repositories têm docblock?
- [ ] Rotas documentadas?
- [ ] README atualizado?
- [ ] CHANGELOG registrado?

## FORMATO CHANGELOG

```markdown
## [YYYY-MM-DD] - v1.x.x

### Adicionado
- Nova funcionalidade X

### Modificado
- Arquivo Y refatorado

### Corrigido
- Bug Z em W

### Removido
- Método obsoleto
```

## INTEGRAÇÃO

Este agente é chamado:
- **APÓS IMPLEMENTADOR** - para documentar código criado
- **ANTES DE CRÍTICO** - para validação final

Este agente alimenta:
- **LEARNING ENGINE** - com padrões de documentação
- **Kilo/IA** - com contexto para próximas tarefas
