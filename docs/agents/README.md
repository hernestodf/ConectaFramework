# Agentes ConectaFramework

5 agentes especializados para desenvolvimento com o ConectaFramework (PHP 8.2+ MVC).

## Sistema Autônomo de Engenharia

Estes agentes formam um ecossistema inteligente de desenvolvimento:

1. **ANALISADOR** - Mapeia arquitetura e fluxos
2. **GUARDIÃO** - Avalia impacto antes de mudanças
3. **DEBUG** - Corrige erros PHP/SQL/Rotas
4. **DATABASE** - SQL + Repository + Migrations
5. **UI/UX** - Views PHP + Formulários
6. **CODEGEN** - Gera módulos completos
7. **REFATORADOR** - Melhora código sem alterar comportamento
8. **TESTADOR** - Simula fluxos críticos
9. **ARQUITETURA** - Planejamento + Relacionamentos
10. **CRÍTICO** - Aprendizado contínuo

## Sistema de Aprendizado

O ecossistema inclui:

- `/ai/context/learned_patterns.md` - Erros e soluções
- `/ai/context/best_practices.md` - Boas práticas
- `/docs/design_system.md` - Design system completo

## Índice

| Arquivo | Agente | Função |
|--------|-------|--------|
| [AGENT_ANALISADOR.md](./AGENT_ANALISADOR.md) | Analisador | Mapeia arquitetura |
| [AGENT_GUARDIAO.md](./AGENT_GUARDIAO.md) | Guardião | Avalia impacto |
| [AGENT_DEBUG.md](./AGENT_DEBUG.md) | Debug | Corrige erros |
| [AGENT_DATABASE.md](./AGENT_DATABASE.md) | Banco de Dados | SQL + Repository |
| [AGENT_UIUX.md](./AGENT_UIUX.md) | UI/UX | Views + Formulários |
| [AGENT_CODEGEN.md](./AGENT_CODEGEN.md) | Módulo Completo | Gera 6 arquivos |
| [AGENT_REFATORADOR.md](./AGENT_REFATORADOR.md) | Refatorador | Melhora código |
| [AGENT_TESTADOR.md](./AGENT_TESTADOR.md) | Testador | Simula fluxos |
| [AGENT_ARCHITECT.md](./AGENT_ARCHITECT.md) | Arquitetura | Planejamento |
| [AGENT_CRITICO.md](./AGENT_CRITICO.md) | Crítico | Aprendizado |

## Como Usar

1. Copie o conteúdo do arquivo MD desejado
2. Cole como primeira mensagem em uma conversa com Claude
3. A partir daí, Claude responderá como especialista neste domínio

## Fluxo de Execução

```
1. ANALISADOR   → Analisa projeto completo
2. GUARDIÃO    → Avalia impacto
3. BACKEND/   → Implementa lógica
   FRONTEND/
4. REFATORADOR → Melhora código
5. TESTADOR   → Simula fluxos
6. CRÍTICO    → Avalia e registra
```

## Onde Salvar os Arquivos Gerados

| Agente | Saída |
|--------|------|
| Database | Repository → `src/Repository/NomeRepository.php` |
| CodeGen | Service → `src/Service/NomeService.php` |
| CodeGen | Controller → `src/Controllers/NomeController.php` |
| UI/UX | Views → `views/modulo/index.php`, `create.php`, `edit.php` |
| Architect | Rotas → adicionar em `public/index.php` |

## Requisitos

- PHP 8.2+
- ConectaFramework instalado
- BaseRepository, BaseService configurados

## HTML Interativo

Para interface gráfica, use o arquivo `ConectaFramework_Agentes.html` na raiz do projeto.

## Contexto de Aprendizado

Arquivos obrigatórioS:

- `/ai/context/learned_patterns.md` - Registro de erros
- `/ai/context/best_practices.md` - Boas práticas
- `/docs/design_system.md` - Design system