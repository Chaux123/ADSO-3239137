<?php

namespace Tests\Unit;

use App\Domain\LoanRules;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LoanRulesTest extends TestCase
{
    public function test_accepts_one_to_three_toys(): void
    {
        LoanRules::validateSelection(['toy-1'], 0, false);
        LoanRules::validateSelection(['toy-1', 'toy-2'], 0, false);
        LoanRules::validateSelection(['toy-1', 'toy-2', 'toy-3'], 0, false);

        $this->assertTrue(true);
    }

    public function test_rejects_empty_selection(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoanRules::validateSelection([], 0, false);
    }

    public function test_rejects_more_than_three_toys(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoanRules::validateSelection(
            ['toy-1', 'toy-2', 'toy-3', 'toy-4'],
            0,
            false
        );
    }

    public function test_rejects_duplicate_toys(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoanRules::validateSelection(
            ['toy-1', 'toy-1'],
            0,
            false
        );
    }

    public function test_rejects_families_with_overdue_loans(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoanRules::validateSelection(['toy-1'], 0, true);
    }

    public function test_rejects_exceeding_three_active_toys(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoanRules::validateSelection(
            ['toy-3', 'toy-4'],
            2,
            false
        );
    }

    public function test_due_date_is_nine_days_after_loan_date(): void
    {
        $loanedAt = new DateTimeImmutable(
            '2026-11-15 10:00:00',
            new DateTimeZone('UTC')
        );

        $dueAt = LoanRules::dueAt($loanedAt);

        $this->assertSame(
            '2026-11-24 10:00:00',
            $dueAt->format('Y-m-d H:i:s')
        );

        $this->assertSame('UTC', $dueAt->getTimezone()->getName());
    }

    public function test_status_is_open_when_items_are_pending(): void
    {
        $items = [
            [
                'due_at' => '2026-11-24 10:00:00',
                'returned_at' => null,
            ],
        ];

        $now = new DateTimeImmutable('2026-11-15 10:00:00');

        $this->assertSame('open', LoanRules::status($items, $now));
    }

    public function test_status_is_overdue_when_due_date_has_passed(): void
    {
        $items = [
            [
                'due_at' => '2026-11-14 10:00:00',
                'returned_at' => null,
            ],
        ];

        $now = new DateTimeImmutable('2026-11-15 10:00:00');

        $this->assertSame('overdue', LoanRules::status($items, $now));
    }

    public function test_status_is_closed_when_all_items_are_returned(): void
    {
        $items = [
            [
                'due_at' => '2026-11-14 10:00:00',
                'returned_at' => '2026-11-15 09:00:00',
            ],
        ];

        $now = new DateTimeImmutable('2026-11-15 10:00:00');

        $this->assertSame('closed', LoanRules::status($items, $now));
    }
}
