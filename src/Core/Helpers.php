<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;

/**
 * Escapes HTML characters to prevent XSS attacks.
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generates an application URL.
 */
function url(string $path = ''): string
{
    $base = rtrim((string)(getenv('APP_URL') ?: ''), '/');
    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $subfolder = ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.') 
            ? str_replace('\\', '/', $scriptDir) 
            : '';
        $base = "{$scheme}://{$host}" . $subfolder;
    }
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

/**
 * Generates an asset URL.
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Gets or validates CSRF token.
 */
function csrf_token(): string
{
    return Csrf::token();
}

/**
 * Generates a hidden HTML CSRF input field.
 */
function csrf_field(): string
{
    $token = csrf_token();
    return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
}

/**
 * Redirects to a path.
 */
function redirect(string $path): void
{
    $target = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
        ? $path
        : url($path);

    header("Location: {$target}", true, 302);
    exit;
}

/**
 * Flash message helper.
 */
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        Session::setFlash($key, $message);
        return null;
    }
    return Session::getFlash($key);
}

/**
 * Retrieves old input flashed into session.
 */
function old(string $key, mixed $default = ''): mixed
{
    return Session::getOldInput($key, $default);
}

/**
 * Gets currently authenticated user or checks authentication.
 */
function auth(): ?array
{
    return Auth::user();
}

/**
 * Checks if current user has one of the specified roles.
 */
function has_role(string ...$roles): bool
{
    return Auth::hasRole(...$roles);
}

/**
 * Formats monetary amounts in Peruvian Soles (PEN).
 */
function format_money(float|int|string|null $amount, string $symbol = 'S/. '): string
{
    $num = (float)($amount ?? 0);
    return $symbol . number_format($num, 2, '.', ',');
}

/**
 * Formats date and time in Peruvian locale format.
 */
function format_date(?string $dateTime, string $format = 'd/m/Y H:i'): string
{
    if (empty($dateTime)) {
        return '-';
    }
    try {
        $dt = new DateTime($dateTime);
        return $dt->format($format);
    } catch (Exception) {
        return $dateTime;
    }
}

