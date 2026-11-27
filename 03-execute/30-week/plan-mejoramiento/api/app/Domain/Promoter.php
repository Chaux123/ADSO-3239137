<?php
namespace App\Domain;

use InvalidArgumentException;

final readonly class Promoter
{
    public string $username;
    public string $role;

    public function __construct(string $username, string $role)
    {
        $username = PromoterRules::username($username);
        if (mb_strlen($username) > 120 || !in_array($role, ['admin', 'promoter'], true)) {
            throw new InvalidArgumentException('Los datos del usuario no son válidos.');
        }
        $this->username = $username;
        $this->role = $role;
    }
}
