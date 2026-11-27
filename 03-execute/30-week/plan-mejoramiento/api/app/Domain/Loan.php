<?php
namespace App\Domain;

use DateTimeImmutable;

final readonly class Loan
{
    /** @param list<LoanItem> $items */
    public function __construct(public array $items) {}

    public function status(DateTimeImmutable $now): string
    {
        $pending = false;
        foreach ($this->items as $item) {
            if ($item->returnedAt === null) {
                $pending = true;
                if ($item->isOverdue($now)) return 'overdue';
            }
        }
        return $pending ? 'open' : 'closed';
    }
}
