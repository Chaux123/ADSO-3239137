<?php

namespace Tests\Feature;

use Tests\TestCase;

final class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'ok',
        ]);
    }

    public function test_health_endpoint_returns_json(): void
    {
        $response = $this->get('/api/v1/health');

        $response->assertStatus(200);

        $this->assertStringContainsString(
            'application/json',
            $response->headers->get('Content-Type')
        );
    }

    public function test_health_endpoint_does_not_require_authentication(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk();
    }
}
