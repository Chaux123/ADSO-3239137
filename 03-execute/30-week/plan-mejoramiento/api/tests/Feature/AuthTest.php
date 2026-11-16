<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    public function test_login_requires_username(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'username',
        ]);
    }

    public function test_login_requires_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'promotor01',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'password',
        ]);
    }

    public function test_login_rejects_empty_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'username',
            'password',
        ]);
    }

    public function test_login_rejects_nonexistent_user(): void
    {
        $query = Mockery::mock();

        $query->shouldReceive('where')
            ->once()
            ->with('username', 'usuarioinexistente')
            ->andReturnSelf();

        $query->shouldReceive('first')
            ->once()
            ->andReturn(null);

        DB::shouldReceive('table')
            ->once()
            ->with('promoters')
            ->andReturn($query);

        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'USUARIOINEXISTENTE',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
    }
}
