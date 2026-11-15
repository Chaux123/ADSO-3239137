<?php

namespace Tests\Unit;

use App\Domain\PromoterRules;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PromoterRulesTest extends TestCase
{
    public function test_username_is_converted_to_lowercase(): void
    {
        $result = PromoterRules::username('JUAN DAVID');

        $this->assertSame('juan david', $result);
    }

    public function test_username_removes_spaces_at_beginning_and_end(): void
    {
        $result = PromoterRules::username('  juan123  ');

        $this->assertSame('juan123', $result);
    }

    public function test_username_accepts_valid_name(): void
    {
        $result = PromoterRules::username('promotor01');

        $this->assertSame('promotor01', $result);
    }

    public function test_username_rejects_empty_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PromoterRules::username('');
    }

    public function test_username_rejects_only_spaces(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PromoterRules::username('   ');
    }

    public function test_role_accepts_promoter(): void
    {
        $result = PromoterRules::role('promoter');

        $this->assertSame('promoter', $result);
    }

    public function test_role_rejects_admin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PromoterRules::role('admin');
    }

    public function test_role_rejects_invalid_role(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PromoterRules::role('visitor');
    }

    public function test_role_rejects_empty_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PromoterRules::role('');
    }

    public function test_role_is_case_sensitive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PromoterRules::role('PROMOTER');
    }
}
