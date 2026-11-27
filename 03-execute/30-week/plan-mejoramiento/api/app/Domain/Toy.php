<?php
namespace App\Domain;

use InvalidArgumentException;

final readonly class Toy
{
    public function __construct(public Quantity $inventory) {}

    public function availableAfterTotalChange(int $newTotal): int
    {
        if ($newTotal < 1) {
            throw new InvalidArgumentException('Las cantidades del inventario son inválidas.');
        }
        $available = $this->inventory->available + $newTotal - $this->inventory->total;
        if ($available < 0) {
            throw new InvalidArgumentException('La cantidad total no permite cubrir los préstamos activos.');
        }
        return $available;
    }
}
