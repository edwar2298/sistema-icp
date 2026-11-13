<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class RoleMiddleware
{
    public function handle(Request $request, string ...$allowedRoles): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }

        if (empty($allowedRoles)) {
            return;
        }

        if (!Auth::hasRole(...$allowedRoles)) {
            http_response_code(403);
            try {
                $html = View::render('errors.403', [
                    'title' => 'Acceso Denegado',
                    'message' => 'No cuenta con los privilegios requeridos para realizar esta operación.'
                ], 'main');
                echo $html;
            } catch (\Throwable) {
                echo "<h1>403 Prohibido</h1><p>No tienes permiso para acceder a este módulo.</p>";
            }
            exit;
        }
    }
}

