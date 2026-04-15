<?php

namespace App\Database;

use App\Core\Env;
use PDO;
use PDOException;

class Connection
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    public static function load(): void
    {
        if (self::$pdo !== null) return;

        $env = Env::get('APP_ENV', 'local');
        
        if ($env === 'production') {
            self::$config = [
                'host' => Env::get('DB_ONLINE_HOST', 'localhost'),
                'port' => Env::get('DB_ONLINE_PORT', 3306),
                'name' => Env::get('DB_ONLINE_NAME', ''),
                'user' => Env::get('DB_ONLINE_USER', 'root'),
                'pass' => Env::get('DB_ONLINE_PASS', ''),
                'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
                'env' => 'production',
            ];
        } else {
            self::$config = [
                'host' => Env::get('DB_LOCAL_HOST', 'localhost'),
                'port' => Env::get('DB_LOCAL_PORT', 3306),
                'name' => Env::get('DB_LOCAL_NAME', ''),
                'user' => Env::get('DB_LOCAL_USER', 'root'),
                'pass' => Env::get('DB_LOCAL_PASS', ''),
                'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
                'env' => 'local',
            ];
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['port'],
            self::$config['name'],
            self::$config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        try {
            self::$pdo = new PDO($dsn, self::$config['user'], self::$config['pass'], $options);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erro na conexão: ' . $e->getMessage());
        }
    }

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            self::load();
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): array
    {
        self::load();
        
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    public static function exec(string $sql, array $params = []): int
    {
        self::load();
        
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }

    public static function lastInsertId(): string
    {
        self::load();
        return self::$pdo->lastInsertId();
    }

    public static function begin(): void
    {
        self::load();
        self::$pdo->beginTransaction();
    }

    public static function commit(): void
    {
        self::load();
        self::$pdo->commit();
    }

    public static function rollback(): void
    {
        self::load();
        self::$pdo->rollBack();
    }

    public static function transaction(callable $callback): mixed
    {
        self::begin();
        
        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            self::rollback();
            throw $e;
        }
    }

    public static function testConnection(): array
    {
        self::load();
        
        try {
            $stmt = self::$pdo->query('SELECT 1 as test');
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso!',
                'host' => self::$config['host'],
                'database' => self::$config['name'],
                'env' => self::$config['env'],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'env' => self::$config['env'] ?? 'unknown',
            ];
        }
    }

    public static function getConfig(): array
    {
        if (empty(self::$config)) {
            self::load();
        }
        return self::$config;
    }
    
    public static function getEnvironment(): string
    {
        return Env::get('APP_ENV', 'local');
    }
}