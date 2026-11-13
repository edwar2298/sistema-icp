<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Router
{
    private array $routes = [];
    private array $groupStack = [];
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): void
    {
        $prefix = '';
        $groupMiddlewares = [];

        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $m = (array)$group['middleware'];
                $groupMiddlewares = array_merge($groupMiddlewares, $m);
            }
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        $allMiddlewares = array_merge($groupMiddlewares, $middlewares);

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $fullPath === '//' ? '/' : $fullPath,
            'handler' => $handler,
            'middlewares' => $allMiddlewares,
        ];
    }

    public function dispatch(Request $request): void
    {
        $requestMethod = $request->getMethod();
        if ($requestMethod === 'HEAD') {
            $requestMethod = 'GET';
        }
        $requestPath = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->compilePattern($route['path']);
            if (preg_match($pattern, $requestPath, $matches)) {
                $params = array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY);

                // Run Middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $this->runMiddleware($middleware, $request);
                }

                // Execute Handler
                $response = $this->executeHandler($route['handler'], $request, $params);
                if ($response instanceof Response) {
                    $response->send();
                }
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        try {
            echo View::render('errors.404', ['title' => 'Página no encontrada'], 'main');
        } catch (\Throwable) {
            echo "<h1>404 - Página no encontrada</h1><p>El recurso solicitado no existe.</p>";
        }
        exit;
    }

    private function compilePattern(string $path): string
    {
        // Replace {param} with named regex capture group (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function runMiddleware(string $middlewareDefinition, Request $request): void
    {
        $parts = explode(':', $middlewareDefinition, 2);
        $name = $parts[0];
        $arg = $parts[1] ?? null;

        $middlewareClass = match ($name) {
            'auth' => \App\Middlewares\AuthMiddleware::class,
            'role' => \App\Middlewares\RoleMiddleware::class,
            'csrf' => \App\Middlewares\CsrfMiddleware::class,
            default => throw new RuntimeException("Middleware desconocido: {$name}"),
        };

        $instance = $this->container->get($middlewareClass);
        $args = $arg !== null ? explode(',', $arg) : [];
        $instance->handle($request, ...$args);
    }

    private function executeHandler(array|callable $handler, Request $request, array $params): mixed
    {
        if (is_callable($handler)) {
            return $handler($request, ...array_values($params));
        }

        [$controllerClass, $method] = $handler;
        $controller = $this->container->get($controllerClass);

        if (!method_exists($controller, $method)) {
            throw new RuntimeException("El método {$method} no existe en el controlador {$controllerClass}");
        }

        return $controller->$method($request, ...array_values($params));
    }
}

