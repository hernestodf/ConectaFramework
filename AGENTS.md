# NovoFramework - Documentação do Agente

## Visão Geral

NovoFramework é um framework PHP moderno com arquitetura MVC, RBAC e roteamento por grupos. Detecta automaticamente ambiente (local/produção) e pasta de instalação.

## Estrutura

```
/var/www/html/novoframework/
├── .env                    # Configurações (NÃO versionar)
├── .env.example           # Modelo de configurações
├── composer.json         # Autoload PSR-4
├── public/
│   ├── index.php       # Entry point
│   ├── css/
│   │   └── styles.css # CSS único (620+ linhas)
│   └── .htaccess     # Rewrite Apache
├── src/
│   ├── Core/
│   │   ├── Application.php  # App principal
│   │   ├── Router.php      # Router com grupos
│   │   ├── Request.php    # Wrapper request
│   │   ├── Response.php   # Response (View/JSON)
│   │   └── Env.php        # Carrega .env
│   ├── Http/
│   │   ├── Controller.php    # Base controller
│   │   ├── Middleware.php   # Interface
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   └── RbacMiddleware.php
│   ├── Database/
│   │   └── Connection.php   # PDO singleton (detecta local/online)
│   ├── Repository/
│   │   ├── BaseRepository.php
│   │   └── UserRepository.php
│   ├── Auth/
│   │   └── Rbac.php        # Roles/Permissions
│   └── Controllers/
│       ├── HomeController.php
│       ├── AuthController.php
│       └── AdminController.php
└── views/
    ├── layout/
    │   ├── header.php
    │   ├── sidebar.php
    │   └── footer.php
    ├── home/
    │   └── index.php
    ├── auth/
    │   └── login.php
    └── admin/
        └── index.php
```

## Ciclo de Request

```
URL (BASE_URL detecta pasta automaticamente)
    ↓
public/index.php
    ↓
Composer Autoload
    ↓
Middleware Stack
    ↓
Router (detecta pasta via BASE_URL)
    ↓
Controller
    ↓
Service
    ↓
Repository
    ↓
Database (detecta local/online via APP_ENV)
    ↓
Response (View)
```

## Configuração .env

### Ambiente Local (desenvolvimento)
```env
APP_ENV=local
BASE_URL=http://localhost/novoframework/public

# DB Local
DB_LOCAL_HOST=localhost
DB_LOCAL_PORT=3306
DB_LOCAL_NAME=novoframework
DB_LOCAL_USER=root
DB_LOCAL_PASS=Profox123
```

### Ambiente Produção (cPanel subpasta)
```env
APP_ENV=production
BASE_URL=https://seudominio.com/novoframework

# DB Online
DB_ONLINE_HOST=localhost
DB_ONLINE_PORT=3306
DB_ONLINE_NAME=seudominio_novoframework
DB_ONLINE_USER=seudominio_admin
DB_ONLINE_PASS=sua_senha
```

### Ambiente Produção (cPanel raiz)
```env
APP_ENV=production
BASE_URL=https://seudominio.com

# DB Online (mesma configuração acima)
```

### Como funciona a detecção

| Variável | Função |
|----------|--------|
| `APP_ENV=local` | Usa DB_LOCAL_* |
| `APP_ENV=production` | Usa DB_ONLINE_* |
| `BASE_URL` com subpasta | Remove caminho das rotas automaticamente |
| `BASE_URL` sem subpasta | Usa rotas diretas |

## Rotas e Grupos

Grupos evitam conflitos em sistemas grandes:

```php
$app->router()->get('/', [HomeController::class, 'index']);
$app->router()->get('/test', [HomeController::class, 'test']);

$app->router()->group('/auth', function($router) {
    $router->get('/login', [AuthController::class, 'login']);
    $router->post('/login', [AuthController::class, 'doLogin']);
});

$app->router()->group('/admin', function($router) {
    $router->get('/', [AdminController::class, 'index']);
}, [App\Http\Middleware\AuthMiddleware::class]);
```

### Grupos disponíveis

- `/auth` - Autenticação
- `/admin` - Área administrativa (protegida)
- `/api` - Endpoints API

## RBAC (Roles)

```php
// Roles disponíveis
$roles = ['guest', 'user', 'manager', 'admin'];

// Verificar permissão
Rbac::check('dashboard');     // true para user, manager, admin
Rbac::check('users');         // true apenas admin
Rbac::hasRole('admin');      // true se for admin

// Verificar se está logado
Rbac::isGuest();             // true se não logado
Rbac::isAdmin();             // true se admin
```

## CSS Unificado

Todo o CSS está em `/public/css/styles.css` (620+ linhas):
- **Nenhum inline style**
- Classes utilitárias (btn-cyan, badge-green, etc)
- Layout completo (sidebar, topbar, cards, tabelas, etc)

JS fica em cada arquivo view (inline).

## Banco de Dados

### Tabela users

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('guest','user','manager','admin') DEFAULT 'user',
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Comando para popular

```bash
./deploy.sh
```

## Comandos Úteis

### Testar API

```bash
curl http://localhost/novoframework/public/test
```

### Verificar conexão

```bash
php -r "require '/var/www/html/novoframework/vendor/autoload.php'; print_r(\App\Database\Connection::testConnection());"
```

### Ver rotas

Adicione no index.php temporariamente:
```php
$app->router()->printRoutes();
```

## Deploy

### 1. Exportar DB local

```bash
mysqldump -u root -pProfox123 novoframework > database/dump.sql
```

### 2. Enviar arquivos (excluir .env e vendor)

```bash
rsync -avz --exclude=vendor --exclude=.env --exclude=.git public/ user@servidor:/home/user/public_html/novoframework/
```

### 3. Criar .env no servidor de produção

Preencha DB_ONLINE_* com os dados do cPanel

### 4. Importar DB no servidor

```bash
mysql -u usuario_cpanel -p senha_cpanel nome_banco < database/dump.sql
```

### 5. Alterar APP_ENV para production

No servidor, altere o .env para:
```env
APP_ENV=production
```

## Erros Comuns

| Erro | Solução |
|------|--------|
| Class not found | Execute `composer dump-autoload` |
| Connection refused | Verificar DB_HOST no .env |
| Table not found | Executar migrations ou deploy.sh |
| 404 no Servidor | Verificar .htaccess e BASE_URL |
| Rota não encontrada | Verificar se BASE_URL está correto no .env |

## Padrões de Código

- **Namespace**: `App\` (PSR-4)
- **Controller**: `App\Controllers\<Nome>Controller`
- **Repository**: `App\Repository\<Nome>Repository`
- **Service**: `App\Service\<Nome>Service`
- **Middleware**: `App\Http\Middleware\<Nome>Middleware`
- **View**: `views/<pasta>/<nome>.php`

## Arquivos Ignorados (.gitignore)

```
/vendor/
/.env
/storage/*.sqlite
/storage/cache/*
*.log
database/dump*.sql
```

---

**Para dúvidas:** Analise o código fonte em `/src/` ou consulte a documentação inline nos arquivos.