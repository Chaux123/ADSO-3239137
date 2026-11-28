<?php

namespace Tests\Feature;

use App\Http\Middleware\JwtMiddleware;
use Tests\TestCase;

final class LoanTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Authentication is tested in a separate test file.
        $this->withoutMiddleware(JwtMiddleware::class);
        $this->withoutMiddleware(\App\Http\Middleware\StaffMiddleware::class);
    }

    public function test_loan_requires_family_id(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'toy_ids' => [
                '00000000-0000-4000-8000-000000000001',
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'family_id',
        ]);
    }

    public function test_loan_requires_toy_ids(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'family_id' => '00000000-0000-4000-8000-000000000001',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'toy_ids',
        ]);
    }

    public function test_loan_rejects_invalid_family_id(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'family_id' => 'invalid-family',
            'toy_ids' => [
                '00000000-0000-4000-8000-000000000001',
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'family_id',
        ]);
    }

    public function test_loan_rejects_empty_toy_selection(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'family_id' => '00000000-0000-4000-8000-000000000001',
            'toy_ids' => [],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'toy_ids',
        ]);
    }

    public function test_loan_rejects_more_than_three_toys(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'family_id' => '00000000-0000-4000-8000-000000000001',
            'toy_ids' => [
                '00000000-0000-4000-8000-000000000002',
                '00000000-0000-4000-8000-000000000003',
                '00000000-0000-4000-8000-000000000004',
                '00000000-0000-4000-8000-000000000005',
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'toy_ids',
        ]);
    }

    public function test_loan_rejects_duplicate_toys(): void
    {
        $toyId = '00000000-0000-4000-8000-000000000002';

        $response = $this->postJson('/api/v1/loans', [
            'family_id' => '00000000-0000-4000-8000-000000000001',
            'toy_ids' => [
                $toyId,
                $toyId,
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'toy_ids.0',
            'toy_ids.1',
        ]);
    }

    public function test_loan_rejects_invalid_toy_id(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'family_id' => '00000000-0000-4000-8000-000000000001',
            'toy_ids' => [
                'invalid-toy',
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'toy_ids.0',
        ]);
    }

    public function test_loan_search_rejects_invalid_family_id(): void
    {
        $response = $this->getJson(
            '/api/v1/loans?family_id=invalid-family'
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'family_id',
        ]);
    }

    public function test_loan_search_rejects_invalid_status(): void
    {
        $response = $this->getJson(
            '/api/v1/loans?status=invalid'
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'status',
        ]);
    }
}
