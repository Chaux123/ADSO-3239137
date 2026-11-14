
<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Illuminate\Http\Request;

abstract class BaseController
{
    protected function admin(Request $request): void
    {
        $promoter = $request->attributes->get('promoter');

        if ($promoter === null) {
            throw new ApiException(
                'UNAUTHORIZED',
                'Debe iniciar sesión para continuar.',
                401
            );
        }

        if ($promoter->role !== 'admin') {
            throw new ApiException(
                'FORBIDDEN_ROLE',
                'No tiene permiso para realizar esta acción.',
                403
            );
        }
    }
}
