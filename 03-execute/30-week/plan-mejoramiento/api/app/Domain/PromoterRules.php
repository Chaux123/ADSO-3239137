
<?php

namespace App\Domain;

use InvalidArgumentException;

final class PromoterRules
{
    public static function username(string $name): string
    {
        $username = strtolower(trim($name));

        if ($username === '') {
            throw new InvalidArgumentException(
                'El nombre de usuario es obligatorio.'
            );
        }

        return $username;
    }

    public static function role(string $role): string
    {
        if ($role !== 'promoter') {
            throw new InvalidArgumentException(
                'Solo se permite crear usuarios promotores.'
            );
        }

        return $role;
    }
}
