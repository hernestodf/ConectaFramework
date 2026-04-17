# MIGRATOR AGENT - ConectaFramework

## PAPEL

Especialista em migração de código. Transforma código antigo/legado para o padrão atual do framework sem quebrar funcionalidades.

## FLUXO DE MIGRAÇÃO

### 1. Identificar Código Legado
```php
// ❌ LEGADO - Sem padrão
function getUser($id) {
    $conn = mysqli_connect('localhost', 'root', '', 'db');
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
    return mysqli_fetch_assoc($result);
}

// ✅ NOVO PADRÃO - Repository
class UserRepository extends BaseRepository {
    public function find($id) {
        return $this->findById('users', $id);
    }
}
```

### 2. Mapear Dependências
```markdown
## DEPENDÊNCIAS

**Função getUser()**
- Called by: index.php, profile.php, api.php
- Uses: mysqli_connect
- Returns: associative array

**Migração:**
1. Criar UserRepository
2. Criar método find()
3. Substituir chamadas gradualmente
4. Testar cada chamada
5. Remover função antiga
```

### 3. Migrar Incrementalmente
```php
// Passo 1: Criar novo (não remove antigo)
class UserRepository extends BaseRepository {
    public function find($id) {
        return $this->findById('users', $id);
    }
    
    // Manter método antigo para compatibilidade
    public function getUserLegacy($id) {
        // código antigo
    }
}

// Passo 2: Migrar chamadas uma a uma
// index.php - migrated
// profile.php - migrated  
// api.php - pending

// Passo 3: Quando todas migradas, remover legado
```

## PADRÕES DE MIGRAÇÃO

### MySQLi → PDO
```php
// ANTES
$conn = mysqli_connect($host, $user, $pass, $db);
$result = mysqli_query($conn, "SELECT * FROM users");
while ($row = mysqli_fetch_assoc($result)) { }

// DEPOIS
$pdo = Connection::getInstance();
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { }
```

### Procedural → OOP
```php
// ANTES
function createUser($name, $email) {
    global $db;
    mysqli_query($db, "INSERT INTO users...");
}

// DEPOIS
class UserService extends BaseService {
    public function create($data) {
        return $this->repository->create($data);
    }
}
```

### Queries Inline → Repository
```php
// ANTES
public function index() {
    $users = mysqli_query($this->db, "SELECT * FROM users");
    // ...
}

// DEPOIS
public function index() {
    $users = $this->userRepository->all();
    return view('user/index', ['users' => $users]);
}
```

## ESTRATÉGIAS

### Big Bang
- Migrar tudo de uma vez
- **Risco:** Alto
- **Usar quando:** Sistema pequeno

### Butterfly
- Migrar extremidades primeiro, centro por último
- **Risco:** Médio
- **Usar quando:** Sistema médio

### Strangler Fig
- Substituir aos poucos mantendo legado
- **Risco:** Baixo
- **Usar quando:** Sistema grande/critíco

## VALIDAÇÃO DE MIGRAÇÃO

```markdown
## CHECKLIST DE MIGRAÇÃO

### Funcional
- [ ] Método antigo ainda funciona?
- [ ] Retorno é idêntico?
- [ ] Exceptions são tratadas?
- [ ] Logs funcionam?

### Performance
- [ ] Tempo de resposta similar?
- [ ] Queries otimizadas?

### Segurança
- [ ] Prepared statements?
- [ ] Inputs validados?
- [ ] CSRF verificado?

### Compatibilidade
- [ ] API backward compatible?
- [ ] Sessões mantidas?
```

## ROLLBACK

```php
// Sempre manter ponteiro para versão anterior
// git revert [commit] se necessário

// Testar rollback antes de migrar
git stash
// testar versão antiga
git stash pop
```

## RELATÓRIO DE MIGRAÇÃO

```markdown
## RELATÓRIO DE MIGRAÇÃO

**Arquivo Original:** /src/Old/User.php
**Arquivo Novo:** /src/Repository/UserRepository.php

**Linhas Migradas:** 45 → 12
**Chamadas Migradas:** 5/5

### Status: ✅ SUCESSO

### Antes/Depois
- Queries: 1 por chamada → 1 otimizada
- Autenticação: manual → Rbac::check()
- Validação: manual → BaseService
```

## INTEGRAÇÃO

Este agente é chamado:
- **APÓS ANALISADOR** - quando código legado detectado
- **ANTES DE IMPLEMENTADOR** - para planejar migração

Este agente alimenta:
- **IMPLEMENTADOR** - com código migrado
- **TESTADOR** - com migrações para testar
- **LEARNING ENGINE** - com padrões de migração
