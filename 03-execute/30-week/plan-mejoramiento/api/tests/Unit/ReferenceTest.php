<?php

namespace Tests\Unit;

use App\Domain\Reference;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ReferenceTest extends TestCase
{
    public function test_converts_reference_to_uppercase(): void
    {
        $result = Reference::normalize('toy123');

        $this->assertSame('TOY123', $result);
    }

    public function test_removes_spaces_at_beginning_and_end(): void
    {
        $result = Reference::normalize('  ABC-123  ');

        $this->assertSame('ABC-123', $result);
    }

    public function test_accepts_valid_reference_with_letters_and_numbers(): void
    {
        $result = Reference::normalize('ABC123');

        $this->assertSame('ABC123', $result);
    }

    public function test_accepts_reference_with_hyphens(): void
    {
        $result = Reference::normalize('TOY-001');

        $this->assertSame('TOY-001', $result);
    }

    public function test_rejects_empty_reference(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Reference::normalize('');
    }

    public function test_rejects_reference_with_one_character(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Reference::normalize('A');
    }

    public function test_rejects_reference_longer_than_fifty_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Reference::normalize(str_repeat('A', 51));
    }

    public function test_rejects_reference_with_internal_spaces(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Reference::normalize('TOY 123');
    }

    public function test_rejects_reference_with_special_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Reference::normalize('TOY@123');
    }

    public function test_accepts_reference_with_two_characters(): void
    {
        $result = Reference::normalize('A1');

        $this->assertSame('A1', $result);
    }

    public function test_accepts_reference_with_fifty_characters(): void
    {
        $reference = str_repeat('A', 50);

        $result = Reference::normalize($reference);

        $this->assertSame($reference, $result);
    }
}
