
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
        if (
            $oldTotal < 1 ||
            $available < 0 ||
            $available > $oldTotal ||
            $newTotal < 1
        ) {
            throw new InvalidArgumentException(
                'Las cantidades del inventario son inválidas.'
            );
        }

        $newAvailable = $available + $newTotal - $oldTotal;

        if ($newAvailable < 0) {
            throw new InvalidArgumentException(
                'La cantidad total no permite cubrir los préstamos activos.'
            );
        }

        return $newAvailable;
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
