<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    private static string $viewsPath;

    public static function setViewsPath(string $path): void
    {
        self::$viewsPath = rtrim($path, '/\\');
    }

    public static function getViewsPath(): string
    {
        if (!isset(self::$viewsPath)) {
            self::$viewsPath = dirname(__DIR__, 2) . '/views';
        }
        return self::$viewsPath;
    }

    public static function render(string $view, array $data = [], ?string $layout = 'main'): string
    {
        $viewFile = self::getViewsPath() . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("Vista '{$view}' no encontrada en: {$viewFile}");
        }

        // Extract view data safely into local scope
        extract($data, EXTR_SKIP);

        // Capture inner view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // If no layout is requested (e.g. AJAX partial, direct view, or print standalone)
        if ($layout === null) {
            return $content;
        }

        $layoutFile = self::getViewsPath() . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Layout '{$layout}' no encontrado en: {$layoutFile}");
        }

        // Capture layout wrapping inner view
        ob_start();
        require $layoutFile;
        return ob_get_clean();
    }

    public static function partial(string $partial, array $data = []): void
    {
        $partialFile = self::getViewsPath() . '/partials/' . str_replace('.', '/', $partial) . '.php';
        if (!file_exists($partialFile)) {
            throw new RuntimeException("Parcial '{$partial}' no encontrado en: {$partialFile}");
        }
        extract($data, EXTR_SKIP);
        require $partialFile;
    }
}

