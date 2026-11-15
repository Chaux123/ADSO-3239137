<?php

namespace Tests\Unit;

use App\Domain\ToyRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ToyRulesTest extends TestCase
{
    public function test_can_increase_total_inventory(): void
    {
        $result = ToyRules::changeTotal(5, 3, 8);

        $this->assertSame(6, $result);
    }

    public function test_can_decrease_total_inventory_when_units_are_available(): void
    {
        $result = ToyRules::changeTotal(8, 6, 5);

        $this->assertSame(3, $result);
    }

    public function test_cannot_decrease_total_below_active_loans(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ToyRules::changeTotal(8, 2, 5);
    }

    public function test_rejects_invalid_inventory_quantities(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ToyRules::changeTotal(5, 6, 8);
    }

    public function test_withdraw_decreases_available_units(): void
    {
        $result = ToyRules::withdraw(3);

        $this->assertSame(2, $result);
    }

    public function test_cannot_withdraw_when_stock_is_zero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ToyRules::withdraw(0);
    }

    public function test_restock_increases_available_units(): void
    {
        $result = ToyRules::restock(2, 5);

        $this->assertSame(3, $result);
    }

    public function test_cannot_restock_above_total_inventory(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ToyRules::restock(5, 5);
    }

    public function test_cannot_use_negative_available_inventory(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ToyRules::restock(-1, 5);
    }

    public function test_rejects_zero_total_inventory(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ToyRules::changeTotal(5, 3, 0);
    }
}
