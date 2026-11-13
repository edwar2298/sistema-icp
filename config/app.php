<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'ICP Informatics Company of Perú',
    'env' => getenv('APP_ENV') ?: 'development',
    'debug' => filter_var(getenv('APP_DEBUG') ?: true, FILTER_VALIDATE_BOOLEAN),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => getenv('APP_TIMEZONE') ?: 'America/Lima',
    'session_lifetime' => (int)(getenv('SESSION_LIFETIME') ?: 7200),
    'failed_login_limit' => (int)(getenv('FAILED_LOGIN_LIMIT') ?: 5),
    'lockout_time_minutes' => (int)(getenv('LOCKOUT_TIME_MINUTES') ?: 15),
];

