<?php

declare(strict_types=1);

/**
 * Built-in PHP development server router
 * Usage: php -S localhost:8000 router.php
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static assets directly if they exist
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    return false; // let PHP server serve the file directly
}

// Route to Front Controller
require_once __DIR__ . '/public/index.php';

