<?php
namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;

final class StaffMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->attributes->get('promoter');
        if (!$user || !in_array($user->role, ['admin', 'promoter'], true)) {
            throw new ApiException('FORBIDDEN_ROLE', 'No tienes permiso para realizar esta acción.', 403);
        }
        return $next($request);
    }
}
