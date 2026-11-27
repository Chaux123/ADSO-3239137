<?php
namespace App\Http\Controllers;

use App\Application\Authenticate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends BaseController
{
    public function login(Request $request, Authenticate $authenticate): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        return response()->json($authenticate->execute($data['username'], $data['password']));
    }
}
