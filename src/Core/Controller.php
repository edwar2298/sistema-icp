<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'main'): Response
    {
        $html = View::render($view, $data, $layout);
        return Response::html($html);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $path): Response
    {
        $target = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : url($path);

        return Response::redirect($target);
    }

    protected function validateCsrf(Request $request): void
    {
        $token = $request->input('_csrf_token') ?? ($request->input('csrf_token') ?? '');
        if (!Csrf::validate($token)) {
            Session::setFlash('error', 'Token de seguridad inválido o sesión expirada. Por favor, reintente.');
            redirect($request->getPath());
        }
    }

    /**
     * Basic validation helper for form inputs.
     * Rules format: ['field' => 'required|min:3|numeric|email|in:A,B']
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && ($value === null || trim((string)$value) === '')) {
                    $errors[$field] = "El campo '{$field}' es obligatorio.";
                    break;
                }

                if ($value === null || trim((string)$value) === '') {
                    continue; // Skip further checks if empty and not required
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (is_numeric($value) && (float)$value < $min) {
                        $errors[$field] = "El campo '{$field}' debe ser mayor o igual a {$min}.";
                    } elseif (is_string($value) && mb_strlen($value) < $min) {
                        $errors[$field] = "El campo '{$field}' debe tener al menos {$min} caracteres.";
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (is_numeric($value) && (float)$value > $max) {
                        $errors[$field] = "El campo '{$field}' no debe superar {$max}.";
                    } elseif (is_string($value) && mb_strlen($value) > $max) {
                        $errors[$field] = "El campo '{$field}' no debe exceder {$max} caracteres.";
                    }
                }

                if ($rule === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = "El campo '{$field}' debe ser numérico.";
                }

                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "El campo '{$field}' debe ser un correo electrónico válido.";
                }

                if (str_starts_with($rule, 'in:')) {
                    $options = explode(',', substr($rule, 3));
                    if (!in_array($value, $options, true)) {
                        $errors[$field] = "El valor seleccionado para '{$field}' no es válido.";
                    }
                }
            }
        }

        if (!empty($errors)) {
            Session::flashInput($data);
            Session::setFlash('errors', $errors);
            Session::setFlash('error', reset($errors));
            return ['valid' => false, 'errors' => $errors];
        }

        return ['valid' => true, 'errors' => []];
    }
}

