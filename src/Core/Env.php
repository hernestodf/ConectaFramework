<?php

namespace App\Core;

class Env
{
    private static array $cache = [];
    private static bool $loaded = false;

    public static function load(string $path = null): void
    {
        if (self::$loaded) return;

        $path = $path ?? dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($path)) {
            self::$loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line) || str_starts_with($line, '#')) continue;
            
            if (!str_contains($line, '=')) continue;
            
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
                $value = substr($value, 1, -1);
            } elseif (str_starts_with($value, "'") && str_ends_with($value, "'")) {
                $value = substr($value, 1, -1);
            }
            
            self::$cache[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
        
        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        if (!self::$loaded) self::load();
        
        return self::$cache[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        if (!self::$loaded) self::load();
        
        return isset(self::$cache[$key]);
    }

    public static function all(): array
    {
        if (!self::$loaded) self::load();
        
        return self::$cache;
    }
}