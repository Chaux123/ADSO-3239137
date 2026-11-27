<?php
namespace App\Domain;

final class Reference
{
    public static function normalize(string $value): string
    {
        return (new ToyReference($value))->value;
    }
}
