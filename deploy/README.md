# Deploy - NovoFramework

Script para deploy automático via FTP.

## Requisitos

```bash
pip install -r requirements.txt
```

## Configuração

Preencha os dados FTP no arquivo `.env` na raiz do projeto:

```env
FTP_HOST=ftp.seudominio.com
FTP_PORT=21
FTP_USER=usuario_ftp
FTP_PASS=senha_ftp
FTP_PATH=/public_html/novoframework
```

## Uso

```bash
cd deploy
python3 deploy.py
```

### Menu:

1. **Testar conexão FTP** - Verifica se consegue conectar no servidor
2. **Enviar arquivos via FTP** - Faz upload da pasta public/
3. **Exportar banco de dados local** - Gera dump SQL
4. **Deploy completo** - Exporta DB + envia arquivos
5. **Sair**

## O que é enviado

Arquivos enviados (pasta `public/`):
- PHP
- CSS
- JS
- Imagens
- Views

Arquivos **ignorados**:
- vendor/
- .env
- .git
- *.pyc
- *.sql
- database/
- storage/

## Fluxo completo

```bash
# 1. Exportar banco local (opcional)
mysqldump -u root -pProfox123 novoframework > database/dump.sql

# 2. Enviar via FTP
python3 deploy.py
# Escolher opção 2 ou 4

# 3. No servidor: criar .env de produção com DB_ONLINE_*

# 4. No servidor: importar dump.sql
mysql -u admin -p database < dump.sql
```

## Dicas

- O script detecta arquivos modificados e envia apenas os necessários
- Mostra progresso de upload
- Trata erros de conexão automaticamente