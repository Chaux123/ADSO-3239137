
<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Support\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class AuthController extends BaseController
{
    public function login(
        Request $request,
        JwtService $jwt
    ): JsonResponse {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = strtolower(trim($data['username']));

        // Find the user by normalized username.
        $promoter = DB::table('promoters')
            ->where('username', $username)
            ->first();

        // Do not reveal which credential is incorrect.
        if (
            !$promoter ||
            !Hash::check(
                $data['password'],
                $promoter->password_hash
            )
        ) {
            throw new ApiException(
                'INVALID_CREDENTIALS',
                'Las credenciales proporcionadas son incorrectas.',
                401
            );
        }

        // Generate an access token valid for eight hours.
        $token = $jwt->issue(
            (string) $promoter->id,
            (string) $promoter->role
        );

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 28800,
            'role' => $promoter->role,
        ], 200);
    }
}
