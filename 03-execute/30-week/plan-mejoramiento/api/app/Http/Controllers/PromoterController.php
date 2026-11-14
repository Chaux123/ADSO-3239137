
<?php

namespace App\Http\Controllers;

use App\Domain\PromoterRules;
use App\Exceptions\ApiException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class PromoterController extends BaseController
{
    public function store(Request $request): JsonResponse
    {
        // Only administrators can create promoters.
        $this->admin($request);

        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:120'
            ],
            'password' => [
                'required',
                'string',
                'min:12'
            ],
            'role' => [
                'required',
                'string',
                'in:promoter'
            ],
        ]);

        // Normalize the username.
        $username = PromoterRules::username(
            $data['username']
        );

        // Prevent duplicate usernames.
        if (
            DB::table('promoters')
                ->where('username', $username)
                ->exists()
        ) {
            throw new ApiException(
                'USERNAME_TAKEN',
                'El nombre de usuario ya existe.',
                409
            );
        }

        $id = (string) Str::uuid();

        try {
            DB::table('promoters')->insert([
                'id' => $id,
                'username' => $username,
                'password_hash' => Hash::make(
                    $data['password']
                ),
                'role' => 'promoter'
            ]);
        } catch (QueryException $exception) {
            // Handle concurrent duplicate registrations.
            if ($exception->getCode() === '23505') {
                throw new ApiException(
                    'USERNAME_TAKEN',
                    'El nombre de usuario ya existe.',
                    409
                );
            }

            throw $exception;
        }

        // Never expose passwords or password hashes.
        return response()->json([
            'id' => $id,
            'username' => $username,
            'role' => 'promoter'
        ], 201);
    }
}
