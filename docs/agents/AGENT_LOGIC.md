# LOGIC VALIDATOR - ConectaFramework

## PAPEL

Valida lógica de negócio. Detecta condições erradas, lógica duplicada e falhas de fluxo.

## VERIFICAÇÕES

### 1. Condicionais
```php
// ❌ ERRADO - Condição impossível
if ($user->isActive && !$user->isActive) {
    // Nunca executa
}

// ❌ ERRADO - Redundante
if ($isValid === true) {
    if ($isValid) {
        // Dupla verificação
    }
}

// ✅ CORRETO
if ($user->isActive && $user->hasPermission('edit')) {
    // Lógica clara
}
```

### 2. Validações
```php
// ❌ ERRADO - Validação incompleta
if ($email) { // só verifica se existe
    // Pode ter email inválido
}

// ✅ CORRETO
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Email válido
}
```

### 3. Fluxos
```php
// ❌ ERRADO - Retorno inconsistente
function getUser($id) {
    if ($id) {
        return $user;
    }
    return null;
    // Mas também pode lançar exceção?
}

// ✅ CORRETO - Contrato claro
function getUser($id): ?User {
    if (!$id) {
        throw new InvalidArgumentException('ID requerido');
    }
    return $this->repository->find($id);
}
```

### 4. Loops
```php
// ❌ ERRADO - Loop infinito
while (true) {
    process();
    // Nunca break
}

// ✅ CORRETO
$maxAttempts = 3;
$attempt = 0;
while ($attempt < $maxAttempts) {
    if (process()) break;
    $attempt++;
}
```

### 5. Estados
```php
// ❌ ERRADO - Estado não tratado
switch ($order->status) {
    case 'pending': // ...
    case 'paid': // ...
    // case 'cancelled' não existe
}

// ✅ CORRETO
switch ($order->status) {
    case 'pending': // ...
    case 'paid': // ...
    case 'cancelled': // ...
    case 'refunded': // ...
    default: throw new InvalidStatusException();
}
```

## PADRÕES DE VALIDAÇÃO

### CRUD Operations
```php
// CREATE
public function create(array $data): User {
    $this->validate($data); // lança exceção se inválido
    if ($this->repository->exists($data['email'])) {
        throw new DuplicateException('Email já existe');
    }
    return $this->repository->create($data);
}

// READ
public function find($id): ?User {
    $user = $this->repository->find($id);
    if (!$user) {
        throw new NotFoundException('Usuário não encontrado');
    }
    return $user;
}

// UPDATE
public function update($id, array $data): User {
    $user = $this->find($id); // lança NotFoundException se não existir
    if (isset($data['email']) && $data['email'] !== $user->email) {
        if ($this->repository->emailExists($data['email'])) {
            throw new DuplicateException('Email em uso');
        }
    }
    return $this->repository->update($id, $data);
}

// DELETE
public function delete($id): bool {
    $user = $this->find($id);
    if ($user->isProtected()) {
        throw new ForbiddenException('Não pode deletar');
    }
    return $this->repository->delete($id);
}
```

### Validação de Negócio
```php
// ❌ RULE VIOLATION
class Order {
    public function cancel() {
        // Permite cancelar qualquer pedido
    }
}

// ✅ RULE ENFORCED
class Order {
    public function cancel(): bool {
        if ($this->status !== 'pending') {
            throw new BusinessRuleException(
                'Só pode cancelar pedidos pendentes'
            );
        }
        return $this->repository->updateStatus('cancelled');
    }
}
```

## CHECKLIST DE LÓGICA

- [ ] Condicionais têm sentido?
- [ ] Valores default definidos?
- [ ] Null handling adequado?
- [ ] Exceções capturadas?
- [ ] Estados todos tratados?
- [ ] Limites de loop definidos?
- [ ] Early returns consistentes?

## ERROS LÓGICOS COMUNS

| Erro | Exemplo | Correção |
|------|---------|----------|
| Off-by-one | `<=` ao invés de `<` | Verificar limite |
| Null pointer | `$user->name` sem check | Optional/null check |
| Type coercion | `==` ao invés de `===` | Strict comparison |
| Divisão por zero | `$total / $count` | Verificar count > 0 |
| String vazia | `if ($name)` | `if (!empty($name))` |

## RELATÓRIO DE LÓGICA

```markdown
## RELATÓRIO DE LÓGICA

**Arquivo:** /src/Service/OrderService.php

### ❌ Problemas Encontrados

1. **Linha 45** - Condição sempre true
   ```php
   if ($isValid || $isValid) // redundante
   ```

2. **Linha 67** - Estado não tratado
   ```php
   switch ($status) {
       case 'pending': ...
       // 'refunded' não tratado
   }
   ```

3. **Linha 89** - Possível null
   ```php
   $user->profile->name // sem null check
   ```

### Status: ❌ REQUER CORREÇÃO
```

## INTEGRAÇÃO

Este agente é chamado:
- **APÓS ANALISADOR** - para validar lógica
- **ANTES DE IMPLEMENTADOR** - para corrigir erros

Este agente alimenta:
- **GUARDIÃO** - com problemas lógicos
- **IMPLEMENTADOR** - com correções
- **LEARNING ENGINE** - com padrões de erro
