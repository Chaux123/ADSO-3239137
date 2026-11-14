
<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Support\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

final class JwtMiddleware
{
    public function __construct(
        private JwtService $jwt
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ) {
        $token = $request->bearerToken();

        if (!$token) {
            throw new ApiException(
                'UNAUTHORIZED',
                'Debe iniciar sesión para continuar.',
                401
            );
        }

        try {
            $claims = $this->jwt->verify($token);
        } catch (Throwable $exception) {
            throw new ApiException(
                'INVALID_TOKEN',
                'El token es inválido o ha expirado.',
                401
            );
        }

        $promoterId = $claims['sub'] ?? null;

        if (!is_string($promoterId) || $promoterId === '') {
            throw new ApiException(
                'INVALID_TOKEN',
                'El token no contiene un usuario válido.',
                401
            );
        }

        $promoter = DB::table('promoters')
            ->where('id', $promoterId)
            ->first();

        if (!$promoter) {
            throw new ApiException(
                'UNAUTHORIZED',
                'El usuario no está autorizado.',
                401
            );
        }

        // Make the authenticated promoter available
        // to controllers.
        $request->attributes->set(
            'promoter',
            $promoter
        );

        return $next($request);
    }
}
