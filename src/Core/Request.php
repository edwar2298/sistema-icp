<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private ?array $jsonBody = null;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $server = null,
        ?array $files = null
    ) {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->server = $server ?? $_SERVER;
        $this->files = $files ?? $_FILES;
    }

    public static function capture(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $override = $this->post('_method') ?? ($this->server['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null);
            if ($override && in_array(strtoupper($override), ['PUT', 'DELETE', 'PATCH'], true)) {
                return strtoupper($override);
            }
        }
        return $method;
    }

    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    public function getPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rawurldecode($path);

        // Strip subfolder if running in subdirectory
        $scriptName = str_replace('\\', '/', dirname($this->server['SCRIPT_NAME'] ?? ''));
        $scriptName = rawurldecode($scriptName);
        if ($scriptName !== '/' && $scriptName !== '' && $scriptName !== '.') {
            if (str_starts_with($path, $scriptName)) {
                $path = substr($path, strlen($scriptName));
            } elseif (str_ends_with($scriptName, '/public')) {
                $parentScriptDir = substr($scriptName, 0, -7);
                if ($parentScriptDir !== '' && str_starts_with($path, $parentScriptDir)) {
                    $path = substr($path, strlen($parentScriptDir));
                }
            }
        }

        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function all(): array
    {
        $json = $this->json();
        if (!empty($json)) {
            return array_merge($this->get, $this->post, $json);
        }
        return array_merge($this->get, $this->post);
    }

    public function json(): array
    {
        if ($this->jsonBody !== null) {
            return $this->jsonBody;
        }

        $contentType = $this->server['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $this->jsonBody = is_array($decoded) ? $decoded : [];
        } else {
            $this->jsonBody = [];
        }

        return $this->jsonBody;
    }

    public function isAjax(): bool
    {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            str_contains($this->server['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public function ip(): string
    {
        if (!empty($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return substr($this->server['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
    }
}

