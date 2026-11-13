
<?php

namespace App\Domain;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class LoanRules
{
    public static function validateSelection(
        array $ids,
        int $activeCount,
        bool $hasOverdue
    ): void {
        $quantity = count($ids);

        if ($quantity < 1 || $quantity > 3) {
            throw new InvalidArgumentException(
                'Debe incluir entre 1 y 3 juguetes.'
            );
        }

        if (count(array_unique($ids)) !== $quantity) {
            throw new InvalidArgumentException(
                'No puede repetir un juguete dentro del préstamo.'
            );
        }

        if ($hasOverdue) {
            throw new InvalidArgumentException(
                'La familia tiene préstamos vencidos.'
            );
        }

        if ($activeCount + $quantity > 3) {
            throw new InvalidArgumentException(
                'La familia supera el límite de 3 juguetes.'
            );
        }
    }

    public static function dueAt(
        DateTimeImmutable $loanedAt
    ): DateTimeImmutable {
        return $loanedAt
            ->setTimezone(new DateTimeZone('UTC'))
            ->modify('+9 days');
    }

    public static function status(
        array $items,
        DateTimeImmutable $now
    ): string {
        $pendingItems = array_filter(
            $items,
            fn (array $item): bool =>
                $item['returned_at'] === null
        );

        if (count($pendingItems) === 0) {
            return 'closed';
        }

        foreach ($pendingItems as $item) {
            $dueAt = new DateTimeImmutable($item['due_at']);

            if ($dueAt < $now) {
                return 'overdue';
            }
        }

        return 'open';
    }
}
