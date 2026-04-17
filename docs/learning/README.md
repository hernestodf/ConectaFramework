# Learning Engine - ConectaFramework

Sistema de aprendizado contínuo do ecossistema de agentes.

## Estrutura

```
learning/
├── logs/          # Registros de ciclos
├── reports/        # Relatórios periódicos
├── errors.md      # Banco de erros
├── successes.md   # Banco de acertos
├── metrics.md     # Métricas globais
└── insights.md    # Insights gerados
```

## Fluxo

1. Agente registra ao final de cada ciclo
2. Sistema categoriza (erro/sucesso/métrica)
3. Insights gerados automaticamente
4. Relatórios semanais/mensais

## Agentes

- **VIBE INTERPRETER** - analisa pedidos
- **PLANNER** - planeja execução
- **ANALISADOR** - mapeia sistema
- **GUARDIÃO** - avalia risco
- **IMPLEMENTADOR** - executa código
- **TESTADOR** - valida fluxos
- **VISUAL QA** - valida layout
- **CRÍTICO** - avalia resultado
- **LEARNING ENGINE** - registra aprendizados
