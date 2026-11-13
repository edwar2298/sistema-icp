<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        Session::start();
        $token = Session::get(self::SESSION_KEY);
        if (!$token || !is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function validate(?string $submittedToken): bool
    {
        if (empty($submittedToken)) {
            return false;
        }

        $sessionToken = Session::get(self::SESSION_KEY);
        if (!$sessionToken || !is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }

    public static function regenerate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }
}

