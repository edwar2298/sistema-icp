<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $lifetime = 7200; // 2 hours default
        ini_set('session.gc_maxlifetime', (string)$lifetime);
        ini_set('session.use_strict_mode', '1');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;

        // Manage flash data lifetime
        if (!isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash_next'] = [];
        }
        if (isset($_SESSION['_flash_current'])) {
            unset($_SESSION['_flash_current']);
        }
        $_SESSION['_flash_current'] = $_SESSION['_flash_next'];
        $_SESSION['_flash_next'] = [];
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function setFlash(string $key, mixed $value): void
    {
        self::start();
        $_SESSION['_flash_next'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION['_flash_current'][$key] ?? $default;
    }

    public static function flashInput(array $data): void
    {
        self::start();
        // Exclude passwords
        unset($data['password'], $data['password_confirmation'], $data['_csrf_token']);
        $_SESSION['_flash_next']['_old_input'] = $data;
    }

    public static function getOldInput(string $key, mixed $default = ''): mixed
    {
        self::start();
        $old = $_SESSION['_flash_current']['_old_input'] ?? [];
        return $old[$key] ?? $default;
    }
}

