<?php

namespace Tests\Unit;

use App\Domain\DateRange;
use PHPUnit\Framework\TestCase;

final class DateRangeTest extends TestCase
{
    public function test_accepts_valid_date_range(): void
    {
        DateRange::validate(
            '2026-11-01',
            '2026-11-10'
        );

        $this->assertTrue(true);
    }

    public function test_accepts_same_start_and_end_date(): void
    {
        DateRange::validate(
            '2026-11-01',
            '2026-11-01'
        );

        $this->assertTrue(true);
    }

    public function test_rejects_end_date_before_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateRange::validate(
            '2026-11-10',
            '2026-11-01'
        );
    }

    public function test_rejects_invalid_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateRange::validate(
            'fecha-invalida',
            '2026-11-10'
        );
    }

    public function test_rejects_invalid_end_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateRange::validate(
            '2026-11-01',
            'fecha-invalida'
        );
    }

    public function test_rejects_empty_start_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateRange::validate(
            '',
            '2026-11-10'
        );
    }

    public function test_rejects_empty_end_date(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        DateRange::validate(
            '2026-11-01',
            ''
        );
    }
}
