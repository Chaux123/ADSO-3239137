<?php
namespace App\Domain;

use InvalidArgumentException;

final readonly class Quantity
{
    public function __construct(public int $total, public int $available)
    {
        if ($total < 1 || $available < 0 || $available > $total) {
            throw new InvalidArgumentException('Las cantidades del inventario son inválidas.');
        }
    }
}
