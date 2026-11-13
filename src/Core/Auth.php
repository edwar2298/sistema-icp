<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    private const SESSION_USER_KEY = '_auth_user';

    public static function login(array $user): void
    {
        Session::start();
        Session::regenerate();

        // Store non-sensitive user attributes
        Session::set(self::SESSION_USER_KEY, [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'logged_at' => time(),
        ]);
    }

    public static function logout(): void
    {
        Session::start();
        Session::remove(self::SESSION_USER_KEY);
        Session::destroy();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): ?array
    {
        Session::start();
        return Session::get(self::SESSION_USER_KEY);
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u ? (int)$u['id'] : null;
    }

    public static function name(): ?string
    {
        $u = self::user();
        return $u['name'] ?? null;
    }

    public static function role(): ?string
    {
        $u = self::user();
        return $u['role'] ?? null;
    }

    public static function hasRole(string ...$roles): bool
    {
        $current = self::role();
        if (!$current) {
            return false;
        }

        // Role comparison case-insensitive / accent-insensitive
        $normalize = static fn(string $r): string => mb_strtolower(str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $r));
        $normalizedCurrent = $normalize($current);

        foreach ($roles as $role) {
            if ($normalize($role) === $normalizedCurrent) {
                return true;
            }
        }
        return false;
    }
}

