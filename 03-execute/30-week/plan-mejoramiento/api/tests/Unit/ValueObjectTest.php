<?php
namespace Tests\Unit;

use App\Domain\Quantity;
use App\Domain\ToyReference;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    public function test_reference_is_normalized_and_quantity_is_preserved(): void
    {
        $this->assertSame('TOY-001', (new ToyReference(' toy-001 '))->value);
        $quantity = new Quantity(4, 2);
        $this->assertSame(4, $quantity->total);
        $this->assertSame(2, $quantity->available);
    }

    public function test_invalid_reference_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ToyReference('invalid reference');
    }

    public function test_quantity_cannot_exceed_total(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Quantity(2, 3);
    }

    public function test_quantity_cannot_be_negative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Quantity(2, -1);
    }
    public function test_family_and_promoter_normalize_identity(): void
    {
        $family = new \App\Domain\Family(' Familia ', ' 123 ');
        $promoter = new \App\Domain\Promoter(' USER ', 'promoter');
        $this->assertSame('Familia', $family->fullName);
        $this->assertSame('123', $family->documentNumber);
        $this->assertSame('user', $promoter->username);
    }

    public function test_family_rejects_blank_document(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new \App\Domain\Family('Familia', '  ');
    }

    public function test_promoter_rejects_unknown_role(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new \App\Domain\Promoter('user', 'unknown');
    }
}
