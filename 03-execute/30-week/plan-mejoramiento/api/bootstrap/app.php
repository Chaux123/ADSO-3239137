<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt' => JwtMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $exception, Request $request) {
            if (!$request->is('api/v1/*')) return null;
            $details = [];
            $errors = [];
            $code = 'VALIDATION_ERROR';
            foreach ($exception->validator->failed() as $field => $rules) {
                if (str_starts_with($field, 'toy_ids.') && isset($rules['Distinct'])) $code = 'DUPLICATE_TOY_IN_LOAN';
                if ($field === 'to' && isset($rules['AfterOrEqual'])) $code = 'INVALID_DATE_RANGE';
                foreach ($rules as $rule => $parameters) {
                    $message = match ($rule) {
                        'Required' => 'Este campo es obligatorio.',
                        'Distinct' => 'No se puede repetir el mismo valor.',
                        'AfterOrEqual' => 'La fecha final debe ser igual o posterior a la inicial.',
                        'Date', 'DateFormat' => 'La fecha no tiene un formato válido.',
                        'Uuid' => 'El identificador no es válido.',
                        'Array' => 'Debe proporcionar una lista válida.',
                        'Integer', 'Numeric' => 'Debe proporcionar un número válido.',
                        'Min' => 'El valor es menor que el mínimo permitido.',
                        'Max' => 'El valor supera el máximo permitido.',
                        default => 'El valor proporcionado no es válido.',
                    };
                    $errors[$field][] = $message;
                    $details[] = ['field' => $field, 'issue' => $message];
                }
            }
            return response()->json([
                'code' => $code,
                'message' => 'Los datos proporcionados no son válidos.',
                'details' => $details,
                'errors' => $errors,
            ], 422);
        });
        $exceptions->render(
            function (
                ApiException $exception,
                Request $request
            ) {
                return response()->json(
                    $exception->toArray(),
                    $exception->getStatusCode()
                );
            }
        );
        $exceptions->render(function (Throwable $exception, Request $request) {
            if (!$request->is('api/v1/*')) return null;
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                if ($exception->getStatusCode() !== 404) return null;
                return response()->json(['code' => 'NOT_FOUND', 'message' => 'Recurso no encontrado.', 'details' => []], 404);
            }
            return response()->json(['code' => 'INTERNAL_ERROR', 'message' => 'Ocurrió un error interno.', 'details' => []], 500);
        });
    })
    ->create();
