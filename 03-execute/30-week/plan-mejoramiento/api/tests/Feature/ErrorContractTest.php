<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ErrorContractTest extends TestCase
{
    public function test_unknown_api_route_returns_a_safe_json_error(): void
    {
        $this->getJson('/api/v1/this-route-does-not-exist')
            ->assertNotFound()->assertJsonPath('code', 'NOT_FOUND')->assertJsonPath('details', []);
    }

    public function test_malformed_identifiers_do_not_trigger_database_errors(): void
    {
        foreach (['/toys/not-a-uuid', '/families/not-a-uuid', '/loans/not-a-uuid', '/loans/not-a-uuid/items/not-a-uuid/return'] as $path) {
            $response = str_ends_with($path, '/return')
                ? $this->postJson('/api/v1' . $path)
                : $this->getJson('/api/v1' . $path);
            $response->assertNotFound()->assertJsonPath('code', 'NOT_FOUND');
        }
    }

    public function test_unexpected_exception_does_not_expose_internal_details(): void
    {
        Route::get('/api/v1/error-contract-test', function () {
            throw new \RuntimeException('private internal diagnostic');
        });
        $response = $this->getJson('/api/v1/error-contract-test');
        $response->assertStatus(500)->assertJsonPath('code', 'INTERNAL_ERROR')->assertJsonPath('details', []);
        $this->assertStringNotContainsString('private internal diagnostic', $response->getContent());
    }
}
