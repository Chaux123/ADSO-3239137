<?php

namespace Tests\Unit;

use App\Domain\ValueObjects\DateRange;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function test_end_date_cannot_be_before_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new DateRange(
            new \DateTimeImmutable('2026-11-15'),
            new \DateTimeImmutable('2026-11-14')
        );
    }

    public function test_end_date_can_be_equal_to_start_date(): void
    {
        $range = new DateRange(
            new \DateTimeImmutable('2026-11-15'),
            new \DateTimeImmutable('2026-11-15')
        );

        $this->assertInstanceOf(DateRange::class, $range);
    }

    public function test_end_date_can_be_after_start_date(): void
    {
        $range = new DateRange(
            new \DateTimeImmutable('2026-11-15'),
            new \DateTimeImmutable('2026-11-24')
        );

        $this->assertInstanceOf(DateRange::class, $range);
    }
}
