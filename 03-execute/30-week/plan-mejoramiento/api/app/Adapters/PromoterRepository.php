<?php
namespace App\Adapters;

use Illuminate\Support\Facades\DB;

final class PromoterRepository implements \App\Ports\PromoterPort
{
    public function findByUsername(string $username): ?object
    {
        return DB::table('promoters')->where('username', $username)->first();
    }

    public function findById(string $id): ?object
    {
        return DB::table('promoters')->where('id', $id)->first();
    }

    public function usernameExists(string $username): bool
    {
        return DB::table('promoters')->where('username', $username)->exists();
    }

    public function insert(array $values): void
    {
        try {
            DB::table('promoters')->insert($values);
        } catch (\Illuminate\Database\QueryException $exception) {
            if ($exception->getCode() === '23505') {
                throw new \App\Exceptions\ApiException('USERNAME_TAKEN', 'El nombre de usuario ya existe.', 409);
            }
            throw $exception;
        }
    }
}
