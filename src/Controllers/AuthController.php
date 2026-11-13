<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/dashboard');
        }

        return $this->render('auth.login', [
            'title' => 'Iniciar Sesión - ICP Informatics Company of Perú',
        ], 'auth');
    }

    public function login(Request $request): Response
    {
        $this->validateCsrf($request);

        $email = trim((string)$request->post('email', ''));
        $password = (string)$request->post('password', '');

        if ($email === '' || $password === '') {
            Session::setFlash('error', 'Por favor, ingrese su correo y contraseña.');
            return $this->redirect('/login');
        }

        $result = $this->authService->attempt($email, $password, $request);

        if (!$result['success']) {
            Session::setFlash('error', $result['message']);
            return $this->redirect('/login');
        }

        Session::setFlash('success', 'Bienvenido al Sistema de Gestión de ICP.');
        return $this->redirect('/dashboard');
    }

    public function logout(Request $request): Response
    {
        $this->validateCsrf($request);
        $this->authService->logout($request);
        Session::setFlash('success', 'Sesión cerrada exitosamente.');
        return $this->redirect('/login');
    }
}

