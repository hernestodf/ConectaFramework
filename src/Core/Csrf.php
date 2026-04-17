<?php

namespace App\Core;

class Csrf
{
    private static string $tokenKey = 'csrf_token';

    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::$tokenKey] = $token;
        return $token;
    }

    public static function validate(string $token = null): bool
    {
        if ($token === null) {
            return false;
        }

        $sessionToken = $_SESSION[self::$tokenKey] ?? null;

        if ($sessionToken === null || $token !== $sessionToken) {
            return false;
        }

        return true;
    }

    public static function getToken(): ?string
    {
        return $_SESSION[self::$tokenKey] ?? null;
    }

    public static function regenerate(): string
    {
        return self::generate();
    }

    public static function hasToken(): bool
    {
        return isset($_SESSION[self::$tokenKey]);
    }

    public static function forget(): void
    {
        unset($_SESSION[self::$tokenKey]);
    }
}