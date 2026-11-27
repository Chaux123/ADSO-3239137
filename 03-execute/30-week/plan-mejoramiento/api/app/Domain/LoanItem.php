<?php
namespace App\Domain;

use DateTimeImmutable;

final readonly class LoanItem
{
    public function __construct(public DateTimeImmutable $dueAt, public ?DateTimeImmutable $returnedAt) {}

    public function isOverdue(DateTimeImmutable $now): bool
    {
        return $this->returnedAt === null && $this->dueAt < $now;
    }
}
