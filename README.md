# ConectaFramework 🎯

> Framework PHP MVC enterprise zero-dependências para projetos rápidos.

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://packagist.org/packages/conecta/framework)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Packagist](https://img.shields.io/badge/Packagist-conecta/framework-blue.svg)](https://packagist.org/packages/conecta/framework)

## Por que usar o ConectaFramework?

- ✅ **Zero dependências** - Semvendor hell, PHP puro
- ✅ **PHP 8.2+** - Sem warningsdeprecated
- ✅ **MVC completo** - Router, Controller, Service, Repository
- ✅ **RBAC nativo** - guest, user, manager, admin
- ✅ **Temas dinamicos** - 5 temas prontos
- ✅ **CSRF Protection** - Segurança integrada
- ✅ **Deploy simples** - FTP ou Git

## Instalação

```bash
# Criar novo projeto
composer create-project conecta/framework meu-projeto

# Entrar no projeto
cd meu-projeto

# Copiar .env
cp .env.example .env

# Configurar banco no .env
# APP_ENV=local
# DB_LOCAL_HOST=localhost
# DB_LOCAL_NAME=meu_banco
# DB_LOCAL_USER=root
# DB_LOCAL_PASS=sua_senha

# Servir
php -S localhost:8080 -t public
```

Acesse: http://localhost:8080

## Quick Start

### Criar módulo completo (ex: produtos)

**1. Tabela MySQL:**
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**2. Route (public/index.php):**
```php
$app->router()->group('/products', function($router) {
    $router->get('/', [ProductController::class, 'index']);
    $router->get('/create', [ProductController::class, 'create']);
    $router->post('/store', [ProductController::class, 'store']);
    $router->get('/edit/{id}', [ProductController::class, 'edit']);
    $router->post('/update/{id}', [ProductController::class, 'update']);
    $router->post('/delete/{id}', [ProductController::class, 'delete']);
}, [\App\Http\Middleware\AuthMiddleware::class]);
```

**3. Repository:**
```php
class ProductRepository extends BaseRepository
{
    protected string $table = 'products';
    protected array $fillable = ['name', 'price', 'status'];
}
```

**4. Service:**
```php
class ProductService extends BaseService
{
    public function create(array $data): int
    {
        $this->validateRequired($data, ['name']);
        return $this->repository->create($this->sanitize($data));
    }
}
```

**5. Controller:**
```php
class ProductController extends Controller
{
    public function index(): Response
    {
        return $this->view('product/index', [
            'products' => $this->service->paginate(1, 15)
        ]);
    }
}
```

## Estrutura do Projeto

```
meu-projeto/
├── public/
│   ├── index.php       # Entry point
│   └── css/styles.css # CSS único (620+ linhas)
├── src/
│   ├── Controllers/   # App\Controllers\*Controller
│   ├── Service/       # App\Service\*Service
│   ├── Repository/    # App\Repository\*Repository
│   ├── Auth/         # Rbac
│   ├── Http/Middleware/ # Middlewares
│   ├── Database/     # Connection (PDO)
│   └── Core/         # Router, Application, etc
├── views/
│   ├── layout/       # header, sidebar, footer
│   └── modulo/      # index, create, edit, show
├── config/app.php   # name, theme, etc
├── storage/logs/    #_logs_
└── .env           # Configurações
```

## Temas

Altere em `config/app.php`:
```php
'theme' => [
    'active' => 'blue',  // default | pink | blue | green | dark
]
```

| Tema | Cor |
|------|-----|
| default | Cyan #0B6E8C |
| pink | Rosa #E11D48 |
| blue | Blue #1D4ED8 |
| green | Green #059669 |
| dark | Purple #8B5CF6 |

## RBAC (Roles)

```php
use App\Auth\Rbac;

// Verificar papel
Rbac::hasRole('admin');   // true/false
Rbac::isAdmin();        // true/false

// Verificar permissão
Rbac::check('users');   // apenas admin
```

| Role | Permissões |
|------|----------|
| guest | Visitante |
| user | Básico |
| manager | Relatórios |
| admin | Tudo |

## API Support

O framework suporta WEB e API simultaneamente:

```php
// Rotas API
$app->router()->group('/api', function($router) {
    $router->get('/products', [ProductController::class, 'apiIndex']);
    $router->post('/products', [ProductController::class, 'apiStore']);
});

// Retorno JSON automático
return $this->json(['success' => true, 'data' => $products]);
```

## Deploy

### Via Git (recommendado):
```bash
git add .
git commit -m "Deploy v1.0"
git push origin main
```

### Via FTP:
```bash
python deploy/deploy.py
```

## Comandos Úteis

```bash
# Testar rota
curl http://localhost:8080/test

# Verificar conexão
php -r "require 'vendor/autoload.php'; print_r(\App\Database\Connection::testConnection());"
```

## Stack

| Tecnologia | Detalhe |
|------------|---------|
| PHP | 8.2+ |
| MySQL | PDO |
| CSS | Unificado (1 arquivo) |
| JS | Inline por view |
| Server | Apache/Nginx |

## Licença

MIT License - see [LICENSE](LICENSE) for details.

---

**Desenvolvido com ❤️ por Hernesto Filho**

[GitHub](https://github.com/hernestodf/ConectaFramework) · [Packagist](https://packagist.org/packages/conecta/framework) · [Contato](hernestodf@gmail.com)