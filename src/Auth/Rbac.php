<?php

namespace App\Auth;

use App\Core\Session;
use App\Repository\UserRepository;

class Rbac
{
    private static array $roles = [
        'guest' => [],
        'user' => ['user'],
        'manager' => ['user', 'manager'],
        'admin' => ['user', 'manager', 'admin'],
    ];

    private static array $permissions = [
        'dashboard' => ['user', 'manager', 'admin'],
        'users' => ['admin'],
        'users.create' => ['admin'],
        'users.edit' => ['admin'],
        'users.delete' => ['admin'],
        'reports' => ['manager', 'admin'],
        'settings' => ['admin'],
    ];

    public static function check(string $permission): bool
    {
        $user = self::getUser();
        
        if (!$user) {
            return in_array('guest', self::$permissions[$permission] ?? []);
        }
        
        $userRole = $user['role'] ?? 'guest';
        $allowedRoles = self::$permissions[$permission] ?? ['guest'];
        
        return in_array($userRole, $allowedRoles);
    }

    public static function hasRole(string $role): bool
    {
        $user = self::getUser();
        return ($user['role'] ?? 'guest') === $role;
    }

    public static function hasAnyRole(array $roles): bool
    {
        $user = self::getUser();
        return in_array($user['role'] ?? 'guest', $roles);
    }

    public static function getUser(): ?array
    {
        return Session::get('user');
    }

    public static function setUser(array $user): void
    {
        Session::set('user', $user);
    }

    public static function login(string $email, string $password): bool
    {
        $repo = new UserRepository();
        $user = $repo->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        
        if (!$user['status']) {
            return false;
        }
        
        self::setUser($user);
        Session::regenerate();
        
        return true;
    }

    public static function logout(): void
    {
        Session::forget('user');
    }

    public static function isGuest(): bool
    {
        return self::getUser() === null;
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    public static function isManager(): bool
    {
        return self::hasRole('manager');
    }

    public static function getRoleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'Administrador',
            'manager' => 'Gerente',
            'user' => 'Usuário',
            default => 'Visitante',
        };
    }

    public static function getAllRoles(): array
    {
        return array_keys(self::$roles);
    }

    public static function getAllPermissions(): array
    {
        return array_keys(self::$permissions);
    }
}