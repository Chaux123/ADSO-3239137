<?php

namespace Tests\Feature;

use Tests\TestCase;

final class ToyTest extends TestCase
{
    public function test_toy_search_rejects_invalid_category_id(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\JwtMiddleware::class);
        $response = $this->getJson(
            '/api/v1/toys?category_id=invalid-id'
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'category_id',
        ]);
    }

    public function test_toy_search_rejects_query_longer_than_250_characters(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\JwtMiddleware::class);
        $query = str_repeat('A', 251);

        $response = $this->getJson(
            '/api/v1/toys?q=' . $query
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'q',
        ]);
    }

    public function test_catalog_reads_require_authentication(): void
    {
        foreach (['/categories', '/toys', '/toys/00000000-0000-4000-8000-000000000001', '/toys/00000000-0000-4000-8000-000000000001/cover'] as $path) {
            $this->getJson('/api/v1' . $path)->assertUnauthorized();
        }
    }

    public function test_cannot_create_toy_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/toys', [
            'name' => 'Carro de juguete',
            'brand' => 'Marca de prueba',
            'reference' => 'CAR-001',
            'category_id' => '00000000-0000-4000-8000-000000000001',
            'total_units' => 5,
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_update_toy_without_authentication(): void
    {
        $response = $this->putJson(
            '/api/v1/toys/00000000-0000-4000-8000-000000000001',
            [
                'name' => 'Carro actualizado',
                'brand' => 'Marca de prueba',
                'category_id' => '00000000-0000-4000-8000-000000000002',
                'total_units' => 3,
            ]
        );

        $response->assertStatus(401);
    }

    public function test_cannot_delete_toy_without_authentication(): void
    {
        $response = $this->deleteJson(
            '/api/v1/toys/00000000-0000-4000-8000-000000000001'
        );

        $response->assertStatus(401);
    }

    public function test_cannot_upload_toy_cover_without_authentication(): void
    {
        $response = $this->putJson(
            '/api/v1/toys/00000000-0000-4000-8000-000000000001/cover'
        );

        $response->assertStatus(401);
    }

    public function test_cannot_delete_toy_cover_without_authentication(): void
    {
        $response = $this->deleteJson(
            '/api/v1/toys/00000000-0000-4000-8000-000000000001/cover'
        );

        $response->assertStatus(401);
    }
}
