#!/usr/bin/env python3
"""
NovoFramework - Deploy Script via FTP
用法: python3 deploy.py

Este script facilita o deploy do sistema para produção via FTP.
As configurações são lidas do arquivo .env na raiz do projeto.
"""

import os
import sys
import ftputil
import time
from datetime import datetime

# ============================================================
# LEITURA DO .ENV
# ============================================================

def load_env():
    """Carrega configurações do arquivo .env"""
    env_file = os.path.join(os.path.dirname(__file__), '..', '.env')
    
    if not os.path.exists(env_file):
        print("❌ Arquivo .env não encontrado!")
        sys.exit(1)
    
    config = {}
    with open(env_file, 'r') as f:
        for line in f:
            line = line.strip()
            if line and not line.startswith('#') and '=' in line:
                key, value = line.split('=', 1)
                config[key.strip()] = value.strip()
    
    return config

# ============================================================
# CONFIGURAÇÕES
# ============================================================

# Pastas e arquivos a EXCLUIR do upload
EXCLUDE_DIRS = [
    '.git',
    'vendor',
    'storage',
    'database',
    '__pycache__',
    'node_modules',
]

EXCLUDE_FILES = [
    '.env',
    '.env.example',
    '.gitignore',
    'composer.json',
    'composer.lock',
    'deploy.sh',
    'deploy.py',
    'requirements.txt',
    'README.md',
    'AGENTS.md',
    '*.pyc',
    '*.sql',
    '*.log',
]

# Extensões de código (sempre enviar)
CODE_EXTENSIONS = [
    '.php', '.html', '.css', '.js', '.json', 
    '.xml', '.txt', '.md', '.yaml', '.yml'
]

# ============================================================
# FUNÇÕES DEPLOY
# ============================================================

def test_ftp_connection(config):
    """Testa conexão FTP"""
    print("\n📡 Testando conexão FTP...")
    
    try:
        with ftputil.FTPHost(
            config.get('FTP_HOST', ''),
            config.get('FTP_USER', ''),
            config.get('FTP_PASS', ''),
            session_factory=ftputil.session.session_factory()
        ) as ftp:
            print(f"✅ Conectado: {config.get('FTP_HOST')}")
            print(f"📁 Diretório atual: {ftp.getcwd()}")
            
            # Tenta navegar para o path
            ftp_path = config.get('FTP_PATH', '/')
            if ftp_path and ftp_path != '/':
                try:
                    ftp.chdir(ftp_path)
                    print(f"📁 Path FTP: {ftp_path}")
                except:
                    print(f"⚠️  Path {ftp_path} não existe, criando...")
                    try:
                        ftp.makedirs(ftp_path)
                        ftp.chdir(ftp_path)
                    except:
                        pass
            
            return True
            
    except Exception as e:
        print(f"❌ Erro na conexão: {e}")
        return False

def should_exclude(path):
    """Verifica se o arquivo/pasta deve ser excluído"""
    basename = os.path.basename(path)
    
    # Excluir diretórios
    for excl_dir in EXCLUDE_DIRS:
        if excl_dir in path:
            return True
    
    # Excluir arquivos específicos
    for excl_file in EXCLUDE_FILES:
        if excl_file.startswith('*.'):
            if basename.endswith(excl_file.replace('*', '')):
                return True
        elif basename == excl_file:
            return True
    
    return False

def get_local_files(base_path):
    """Lista todos os arquivos locais a serem enviados"""
    files = []
    
    for root, dirs, filenames in os.walk(base_path):
        # Remove diretórios excluídos da lista
        dirs[:] = [d for d in dirs if not should_exclude(os.path.join(root, d))]
        
        for filename in filenames:
            full_path = os.path.join(root, filename)
            
            if should_exclude(full_path):
                continue
            
            rel_path = os.path.relpath(full_path, base_path)
            stat = os.stat(full_path)
            
            files.append({
                'local_path': full_path,
                'remote_path': rel_path.replace('\\', '/'),
                'size': stat.st_size,
                'mtime': stat.st_mtime,
            })
    
    return files

def upload_files_ftp(config):
    """Envia arquivos via FTP"""
    print("\n📤 Iniciando upload FTP...")
    
    local_path = os.path.join(os.path.dirname(__file__), '..', 'public')
    
    if not os.path.exists(local_path):
        print("❌ Pasta public não encontrada!")
        return False
    
    print(f"📁 Pasta local: {local_path}")
    print(f"🎯 Servidor: {config.get('FTP_HOST')}")
    print(f"📂 Destino: {config.get('FTP_PATH', '/')}")
    
    # Lista arquivos
    print("\n📋 Verificando arquivos...")
    files = get_local_files(local_path)
    print(f"   {len(files)} arquivos para enviar")
    
    try:
        with ftputil.FTPHost(
            config.get('FTP_HOST', ''),
            config.get('FTP_USER', ''),
            config.get('FTP_PASS', ''),
            session_factory=ftputil.session.session_factory()
        ) as ftp:
            
            # Navega para o path destino
            ftp_path = config.get('FTP_PATH', '/')
            if ftp_path and ftp_path != '/':
                try:
                    ftp.chdir(ftp_path)
                except:
                    try:
                        ftp.makedirs(ftp_path)
                        ftp.chdir(ftp_path)
                    except Exception as e:
                        print(f"⚠️  Não criou path: {e}")
            
            uploaded = 0
            skipped = 0
            errors = 0
            total_size = 0
            uploaded_size = 0
            
            print("\n🚀 Enviando arquivos...")
            
            for i, file_info in enumerate(files):
                try:
                    # Verifica se precisa atualizar (comparar tamanho)
                    remote_path = file_info['remote_path']
                    
                    # Cria diretórios remotos se necessário
                    remote_dir = os.path.dirname(remote_path)
                    if remote_dir and remote_dir != '.':
                        try:
                            ftp.makedirs(remote_dir, exist_ok=True)
                        except:
                            pass
                    
                    # Upload do arquivo
                    local_file = file_info['local_path']
                    
                    # mostra progresso
                    percent = ((i + 1) / len(files)) * 100
                    print(f"\r   [{percent:5.1f}%]({i+1}/{len(files)}) {remote_path}", end='')
                    
                    ftp.upload(local_file, remote_path)
                    
                    uploaded += 1
                    uploaded_size += file_info['size']
                    
                except Exception as e:
                    errors += 1
                    print(f"\n   ⚠️  Erro em {remote_path}: {e}")
            
            print(f"\n\n✅ Upload concluído!")
            print(f"   📤 Enviados: {uploaded}")
            print(f"   ⏭️  Ignorados: {skipped}")
            print(f"   ❌ Erros: {errors}")
            print(f"   💾 Tamanho total: {uploaded_size / 1024:.1f} KB")
            
            return True
            
    except Exception as e:
        print(f"\n❌ Erro durante upload: {e}")
        return False

def export_database(config):
    """Exporta banco de dados local"""
    print("\n💾 Exportando banco de dados local...")
    
    db_name = config.get('DB_LOCAL_NAME', 'novoframework')
    db_user = config.get('DB_LOCAL_USER', 'root')
    db_pass = config.get('DB_LOCAL_PASS', '')
    
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    dump_file = f"database/dump_{timestamp}.sql"
    
    # Garante que pasta existe
    os.makedirs('database', exist_ok=True)
    
    cmd = f"mysqldump -u {db_user}"
    if db_pass:
        cmd += f" -p{db_pass}"
    cmd += f" {db_name} > {dump_file}"
    
    print(f"   Executando: mysqldump...")
    print(f"   📁 Salvando em: {dump_file}")
    
    result = os.system(cmd)
    
    if result == 0:
        print(f"✅ Banco exportado com sucesso!")
        return True
    else:
        print(f"❌ Erro ao exportar banco")
        return False

def show_menu():
    """Mostra menu de opções"""
    print("\n" + "="*50)
    print("  NOVOFRAMEWORK - DEPLOY")
    print("="*50)
    print("1. Testar conexão FTP")
    print("2. Enviar arquivos via FTP")
    print("3. Exportar banco de dados local")
    print("4. Deploy completo (exporta DB + upload FTP)")
    print("5. Sair")
    print("="*50)

def main():
    """Função principal"""
    # Carrega configurações
    config = load_env()
    
    # Verifica se FTP está configurado
    if not config.get('FTP_HOST'):
        print("\n⚠️  FTP não configurado no .env")
        print("   Preencha os campos FTP_HOST, FTP_USER, FTP_PASS e FTP_PATH")
        return
    
    while True:
        show_menu()
        choice = input("Escolha uma opção: ").strip()
        
        if choice == '1':
            test_ftp_connection(config)
            
        elif choice == '2':
            upload_files_ftp(config)
            
        elif choice == '3':
            export_database(config)
            
        elif choice == '4':
            print("\n🚀 Deploy completo iniciado...")
            export_database(config)
            upload_files_ftp(config)
            
        elif choice == '5':
            print("\n👋 Até mais!")
            break
        
        else:
            print("\n⚠️  Opção inválida!")

if __name__ == '__main__':
    main()