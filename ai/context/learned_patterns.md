# Learned Patterns - ConectaFramework

## Sistema de Aprendizado

Este arquivo registra erros, soluções e lições aprendidas durante o desenvolvimento doConectaFramework.

---

## Como Registrar Erros

Formato:

```
## Data: YYYY-MM-DD

### Tarefa: Descrição da tarefa

#### Erro:
[Descrição do erro]

#### Solução:
[Como foi resolvido]

#### Aprendizado:
[O que aprender para próximas vezes]
```

---

## Histórico de Erros

### 2024-XX-XX

#### Tarefa: Criar agentes de documentação

#### Erro:
[Adicionar aqui]

#### Solução:
[Adicionar aqui]

#### Aprendizado:
[Adicionar aqui]

---

## Padrões de Erros a Evitar

| Erro | Causa | Solução |
|------|------|--------|
| Namespace errado | App\Controller vs App\Controllers | Sempre usar plural |
| Service sem BaseService | Não estender BaseService | Usar parent::__construct |
| CSS inline | Colocar style="" inline | Mover para styles.css |
| JS inline | Colocar JS inline | Mover para arquivo JS |
| CSRF faltando | Form sem token | Adicionar _csrf_token |
| Rota 404 | Rota não registrada | Verificar public/index.php |
| Rota com método errado | GET vs POST | Verificar método HTTP |

---

## Lições Aprendidas

### Backend

1. **Namespaces devem ter 's' no final**
   - App\Controllers (não App\Controller)
   - App\Services (não App\Service)
   - App\Repositories (não App\Repository)

2. **Services devem estender BaseService**
   - Sempre injetar repository no __construct
   - Usar método parent::__construct()

3. **Repositories devem estender BaseRepository**
   - Definir $table e $fillable
   - Não recriar métodos herdados

### Frontend

1. **Nunca usar CSS inline**
   - Exceptions: display:inline, margin-top:16px
   - Tudo mais vai em public/css/styles.css

2. **Nunca usar JS inline**
   - Tudo vai em public/js/modulo/nome.js

3. **Sempre usar classes do /docs**
   - .fi, .fl, .fg, .col2
   - .btn-cyan, .btn-red, .btn-green
   - .card, .table-default

4. **Sempre incluir layout**
   - header.php, sidebar.php, footer.php

5. **Sempre adicionar CSRF**
   - Em todo form POST

---

## Próximos Passos

- [ ] Revisar este arquivo regularmente
- [ ] Adicionar novos erros aprendidos
- [ ] Atualizar best_practices.md
- [ ] Manter design_system.md atualizado