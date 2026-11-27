<?php

namespace App\Domain;

use InvalidArgumentException;

final class ToyRules
{
    public static function changeTotal(
        int $oldTotal,
        int $available,
        int $newTotal
    ): int {
        return (new Toy(new Quantity($oldTotal, $available)))->availableAfterTotalChange($newTotal);
    }

    public static function withdraw(int $available): int
    {
        if ($available < 1) {
            throw new InvalidArgumentException(
                'No hay unidades disponibles.'
            );
        }

        return $available - 1;
    }

    public static function restock(
        int $available,
        int $total
    ): int {
        if ($total < 1 || $available < 0 || $available >= $total) {
            throw new InvalidArgumentException(
                'Las unidades disponibles no pueden superar el total.'
            );
        }

        return $available + 1;
    }
}
