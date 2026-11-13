<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Session;

class CsrfMiddleware
{
    public function handle(Request $request): void
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $token = $request->input('_csrf_token') ?? ($request->input('csrf_token') ?? '');
            if (!Csrf::validate($token)) {
                Session::setFlash('error', 'Token CSRF inválido o solicitud expirada. Por favor, vuelva a intentarlo.');
                redirect($request->getPath());
            }
        }
    }
}

