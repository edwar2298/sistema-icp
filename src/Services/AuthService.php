<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Request;
use App\Repositories\Contracts\UserRepositoryInterface;
use DateTime;
use RuntimeException;

class AuthService
{
    private UserRepositoryInterface $userRepository;
    private AuditService $auditService;
    private int $maxAttempts = 5;
    private int $lockoutMinutes = 15;

    public function __construct(
        UserRepositoryInterface $userRepository,
        AuditService $auditService
    ) {
        $this->userRepository = $userRepository;
        $this->auditService = $auditService;
    }

    public function attempt(string $email, string $password, Request $request): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->auditService->log('INTENTO_FALLIDO', 'users', null, [
                'email' => $email,
                'reason' => 'Usuario no encontrado'
            ], $request);
            return [
                'success' => false,
                'message' => 'Credenciales incorrectas. Verifique correo y contraseña.'
            ];
        }

        $now = new DateTime();

        // Check if currently locked out
        if (!empty($user['lockout_until'])) {
            $lockoutUntil = new DateTime($user['lockout_until']);
            if ($now < $lockoutUntil) {
                $diff = $now->diff($lockoutUntil);
                $remainingMinutes = ($diff->i) + 1;
                $this->auditService->log('ACCESO_DENEGADO_BLOQUEO', 'users', (string)$user['id'], [
                    'email' => $email,
                    'remaining_minutes' => $remainingMinutes
                ], $request, (int)$user['id']);

                return [
                    'success' => false,
                    'message' => "Esta cuenta ha sido bloqueada temporalmente por 5 intentos fallidos. Intente de nuevo en {$remainingMinutes} minutos."
                ];
            }

            // Lockout period has elapsed: reset attempts
            $this->userRepository->resetFailedAttempts((int)$user['id']);
            $user['failed_attempts'] = 0;
            $user['status'] = 'Activo';
        }

        // Check user active status
        if ($user['status'] === 'Inactivo') {
            return [
                'success' => false,
                'message' => 'Su cuenta se encuentra inactiva. Contacte al administrador.'
            ];
        }

        // Verify password with Bcrypt
        if (!password_verify($password, $user['password_hash'])) {
            $attempts = (int)$user['failed_attempts'] + 1;

            if ($attempts >= $this->maxAttempts) {
                $lockoutUntil = (new DateTime())->modify("+{$this->lockoutMinutes} minutes")->format('Y-m-d H:i:s');
                $this->userRepository->updateFailedAttempts((int)$user['id'], $attempts, $lockoutUntil);

                $this->auditService->log('BLOQUEO_CUENTA', 'users', (string)$user['id'], [
                    'email' => $email,
                    'attempts' => $attempts,
                    'lockout_until' => $lockoutUntil
                ], $request, (int)$user['id']);

                return [
                    'success' => false,
                    'message' => "Ha superado el límite de 5 intentos fallidos. Su cuenta ha sido bloqueada temporalmente por {$this->lockoutMinutes} minutos."
                ];
            }

            $this->userRepository->updateFailedAttempts((int)$user['id'], $attempts, null);
            $remaining = $this->maxAttempts - $attempts;

            $this->auditService->log('INTENTO_FALLIDO', 'users', (string)$user['id'], [
                'email' => $email,
                'attempts' => $attempts
            ], $request, (int)$user['id']);

            return [
                'success' => false,
                'message' => "Contraseña incorrecta. Le quedan {$remaining} intento(s) antes del bloqueo temporal."
            ];
        }

        // Success: Reset failed attempts
        if ((int)$user['failed_attempts'] > 0 || !empty($user['lockout_until'])) {
            $this->userRepository->resetFailedAttempts((int)$user['id']);
        }

        // Set session
        Auth::login($user);

        // Audit login
        $this->auditService->log('LOGIN', 'users', (string)$user['id'], [
            'email' => $user['email'],
            'role' => $user['role']
        ], $request, (int)$user['id']);

        return [
            'success' => true,
            'message' => 'Inicio de sesión exitoso.',
            'user' => $user
        ];
    }

    public function logout(Request $request): void
    {
        $userId = Auth::id();
        if ($userId) {
            $this->auditService->log('LOGOUT', 'users', (string)$userId, null, $request, $userId);
        }
        Auth::logout();
    }
}

