<?php
namespace App\Domain;

use InvalidArgumentException;

final readonly class ToyReference
{
    public string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));
        if (!preg_match('/^[A-Z0-9-]{2,50}$/', $value)) {
            throw new InvalidArgumentException('La referencia debe contener entre 2 y 50 caracteres: letras, números o guiones.');
        }
        $this->value = $value;
    }
}
