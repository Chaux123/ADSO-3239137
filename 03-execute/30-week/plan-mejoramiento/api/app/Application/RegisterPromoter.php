<?php

namespace App\Application;

use App\Domain\Promoter;
use App\Exceptions\ApiException;
use App\Ports\PromoterPort;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class RegisterPromoter
{
    public function __construct(private PromoterPort $promoters) {}

    public function execute(string $name, string $password, string $role): array
    {
        try {
            $promoter = new Promoter($name, $role);
        } catch (\InvalidArgumentException $exception) {
            throw new ApiException('VALIDATION_ERROR', $exception->getMessage(), 422);
        }
        if ($this->promoters->usernameExists($promoter->username)) {
            throw new ApiException('USERNAME_TAKEN', 'El nombre de usuario ya existe.', 409);
        }
        if (!in_array($role, ['promoter', 'customer'], true)) {
            throw new ApiException('VALIDATION_ERROR', 'El rol seleccionado no es válido.', 422);
        }
        $result = ['id' => (string) Str::uuid(), 'username' => $promoter->username, 'role' => $role];
        $this->promoters->insert($result + ['password_hash' => Hash::make($password)]);
        return $result;
    }
}
