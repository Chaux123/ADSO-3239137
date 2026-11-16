<?php

namespace Tests\Feature;

use Tests\TestCase;

final class ProtectedRoutesTest extends TestCase
{
    public function test_cannot_create_toy_without_token(): void
    {
        $response = $this->postJson('/api/v1/toys', [
            'name' => 'Carro de juguete',
            'reference' => 'CAR-001',
            'category_id' => 'category-1',
            'total_units' => 5,
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_create_family_without_token(): void
    {
        $response = $this->postJson('/api/v1/families', [
            'name' => 'Familia Chaux',
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_create_loan_without_token(): void
    {
        $response = $this->postJson('/api/v1/loans', [
            'family_id' => 'family-1',
            'toy_ids' => ['toy-1'],
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_create_promoter_without_token(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'username' => 'promotor01',
            'password' => 'password123',
            'role' => 'promoter',
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_view_reports_without_token(): void
    {
        $response = $this->getJson(
            '/api/v1/reports/most-loaned'
        );

        $response->assertStatus(401);
    }

    public function test_cannot_view_families_without_token(): void
    {
        $response = $this->getJson('/api/v1/families');

        $response->assertStatus(401);
    }

    public function test_cannot_view_loans_without_token(): void
    {
        $response = $this->getJson('/api/v1/loans');

        $response->assertStatus(401);
    }
}
