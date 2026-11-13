<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Debe iniciar sesión para acceder a este recurso.');
            redirect('/login');
        }
    }
}

