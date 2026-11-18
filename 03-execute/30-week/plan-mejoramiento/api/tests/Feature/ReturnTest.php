<?php

namespace Tests\Feature;

use App\Application\ReturnUseCase;
use App\Ports\LoanPort;
use Mockery;
use Tests\TestCase;

final class ReturnTest extends TestCase
{
    public function test_cannot_return_toy_without_authentication(): void
    {
        $response = $this->postJson(
            '/api/v1/loans/loan-1/items/item-1/return'
        );

        $response->assertStatus(401);
    }

    public function test_return_use_case_calls_loan_port(): void
    {
        $loanId = 'loan-123';
        $itemId = 'item-456';

        $expectedResult = [
            'id' => $loanId,
            'status' => 'closed',
        ];

        $store = Mockery::mock(LoanPort::class);

        $store->shouldReceive('returnItem')
            ->once()
            ->with($loanId, $itemId)
            ->andReturn($expectedResult);

        $useCase = new ReturnUseCase($store);

        $result = $useCase->execute(
            $loanId,
            $itemId
        );

        $this->assertSame(
            $expectedResult,
            $result
        );
    }

    public function test_return_use_case_returns_port_result_without_changes(): void
    {
        $expectedResult = [
            'id' => 'loan-789',
            'items' => [
                [
                    'id' => 'item-123',
                    'returned_at' => '2026-11-17T10:00:00Z',
                ],
            ],
        ];

        $store = Mockery::mock(LoanPort::class);

        $store->shouldReceive('returnItem')
            ->once()
            ->with('loan-789', 'item-123')
            ->andReturn($expectedResult);

        $useCase = new ReturnUseCase($store);

        $result = $useCase->execute(
            'loan-789',
            'item-123'
        );

        $this->assertSame(
            $expectedResult,
            $result
        );
    }
}
