#!/bin/bash

echo "========================================="
echo "  NovoFramework - Deploy Script"
echo "========================================="
echo ""

DB_NAME="novoframework"
DB_USER="root"
DB_PASS="Profox123"

echo "[1] Exportando banco de dados local..."
mysqldump -u $DB_USER -p"$DB_PASS" $DB_NAME > database/dump_local.sql
echo "    ✓ Dump salvo em database/dump_local.sql"

echo ""
echo "[2] Listando tabelas..."
mysql -u $DB_USER -p"$DB_PASS" $DB_NAME -e "SHOW TABLES;"
echo ""

echo "[3] Criando tabela users (se não existir)..."
mysql -u $DB_USER -p"$DB_PASS" $DB_NAME << 'EOF'
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('guest','user','manager','admin') DEFAULT 'user',
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
EOF

echo "    ✓ Tabela users verificada"

echo ""
echo "[4] Inserindo usuário de teste..."
mysql -u $DB_USER -p"$DB_PASS" $DB_NAME << 'EOF'
INSERT IGNORE INTO users (name, email, password, role) VALUES 
('Administrador', 'admin@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Gerente', 'gerente@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager'),
('Usuário', 'user@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user')
ON DUPLICATE KEY UPDATE name=name;
EOF

echo "    ✓ Usuários inseridos"

echo ""
echo "========================================="
echo "  Deploy concluído com sucesso!"
echo "========================================="