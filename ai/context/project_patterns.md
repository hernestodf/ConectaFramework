# Padrões do Projeto - ConectaFramework

## PHP

### Controller Pattern
```php
class NomeController extends Controller {
    protected Service $service;
    
    public function __construct(Service $service) {
        $this->service = $service;
    }
    
    public function index() { /* listar */ }
    public function create() { /* mostrar form */ }
    public function store(Request $request) { /* criar */ }
    public function edit($id) { /* mostrar form */ }
    public function update(Request $request, $id) { /* atualizar */ }
    public function destroy($id) { /* deletar */ }
}
```

### Service Pattern
```php
class NomeService extends BaseService {
    public function create(array $data): Entity {
        $this->validate($data);
        return $this->repository->create($data);
    }
}
```

### Repository Pattern
```php
class NomeRepository extends BaseRepository {
    public function findByEmail(string $email) {
        return $this->findOneBy(['email' => $email]);
    }
}
```

### Route Definition
```php
$app->router()->get('/resource', [Controller::class, 'index']);
$app->router()->post('/resource', [Controller::class, 'store']);
$app->router()->group('/admin', fn($r) => ..., [AuthMiddleware::class]);
```

## CSS (Design System)

### Classes Utilitárias
```css
/* Layout */
.container, .row, .col-*, .d-flex, .justify-*, .align-*

/* Spacing */
.m-*, p-*, mt-*, mb-*, me-*, ms-*, mx-*, my-*

/* Cores */
.text-*, .bg-*, .border-*

/* Botões */
.btn, .btn-primary, .btn-secondary, .btn-danger, .btn-outline

/* Cards */
.card, .card-header, .card-body, .card-footer

/* Badges */
.badge, .badge-success, .badge-warning, .badge-danger

/* Formulários */
.form-control, .form-label, .form-text

/* Tipografia */
.text-*, .fw-*, .fs-*
```

### NUNCA USAR
- [ ] CSS inline
- [ ] Estilos hardcoded em elementos
- [ ] Seletores muito específicos (div > ul > li > span)
- [ ] !important

## Views

### Estrutura
```php
<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Título</h1>
    
    <!-- conteúdo -->
    
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
```

### Partials
```
views/
├── components/
│   ├── card.php
│   ├── table.php
│   └── form-group.php
└── ...
```

## Database

### Migration Pattern
```sql
CREATE TABLE tabela (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    role ENUM('guest','user','manager','admin') DEFAULT 'user',
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### CRUD Operations
- **Create:** INSERT com prepared statements
- **Read:** SELECT com WHERE, LIMIT para paginação
- **Update:** UPDATE com validação de ownership
- **Delete:** Soft delete preferido (status=0)

## Naming Conventions

| Tipo | Padrão | Exemplo |
|------|--------|---------|
| Classes | PascalCase | UserController |
| Métodos | camelCase | getUserById |
| Variáveis | camelCase | userName |
| Constantes | UPPER_SNAKE | MAX_RETRY |
| Arquivos | kebab-case | user-controller.php |
| Tabelas | snake_case | user_profiles |
| Colunas | snake_case | created_at |
| Routes | kebab-case | /user-profiles |
