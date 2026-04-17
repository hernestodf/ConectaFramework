# Agentes ConectaFramework

5 agentes especializados para desenvolvimento com o ConectaFramework (PHP 8.2+ MVC).

## Índice

| Arquivo | Agente | Função |
|---------|-------|--------|
| [AGENT_DEBUG.md](./AGENT_DEBUG.md) | Debug | Corrige erros PHP/SQL/Rotas |
| [AGENT_DATABASE.md](./AGENT_DATABASE.md) | Banco de Dados | SQL + Repository + Migrations |
| [AGENT_UIUX.md](./AGENT_UIUX.md) | UI/UX | Views PHP + Formulários |
| [AGENT_CODEGEN.md](./AGENT_CODEGEN.md) | Módulo Completo | Gera 6 arquivos do zero |
| [AGENT_ARCHITECT.md](./AGENT_ARCHITECT.md) | Arquitetura | Planejamento + Relacionamentos |

## Como Usar

1. Copie o conteúdo do arquivo MD desejado
2. Cole como primeira mensagem em uma conversa com Claude
3. A partir daí, Claude responderá como especialista neste domínio

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