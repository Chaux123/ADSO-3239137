<?php

namespace Tests\Feature;

use App\Exceptions\ApiException;
use App\Http\Controllers\PromoterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

final class PromoterTest extends TestCase
{
    private function adminRequest(array $data): Request
    {
        $request = Request::create(
            '/api/v1/users',
            'POST',
            $data
        );

        $request->attributes->set('promoter', (object) [
            'id' => 'admin-1',
            'role' => 'admin',
        ]);

        return $request;
    }

    public function test_promoter_creation_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/users', [
            'username' => 'promotor01',
            'password' => 'secure-password-123',
            'role' => 'promoter',
        ]);

        $response->assertStatus(401);
    }

    public function test_promoter_role_cannot_create_users(): void
    {
        $request = Request::create(
            '/api/v1/users',
            'POST',
            [
                'username' => 'promotor02',
                'password' => 'secure-password-123',
                'role' => 'promoter',
            ]
        );

        $request->attributes->set('promoter', (object) [
            'id' => 'promoter-1',
            'role' => 'promoter',
        ]);

        $this->expectException(ApiException::class);

        (new PromoterController())->store($request);
    }

    public function test_promoter_creation_requires_username(): void
    {
        $request = $this->adminRequest([
            'password' => 'secure-password-123',
            'role' => 'promoter',
        ]);

        $this->expectException(ValidationException::class);

        (new PromoterController())->store($request);
    }

    public function test_promoter_creation_requires_password(): void
    {
        $request = $this->adminRequest([
            'username' => 'promotor01',
            'role' => 'promoter',
        ]);

        $this->expectException(ValidationException::class);

        (new PromoterController())->store($request);
    }

    public function test_promoter_password_requires_twelve_characters(): void
    {
        $request = $this->adminRequest([
            'username' => 'promotor01',
            'password' => '123456',
            'role' => 'promoter',
        ]);

        $this->expectException(ValidationException::class);

        (new PromoterController())->store($request);
    }

    public function test_promoter_creation_rejects_admin_role(): void
    {
        $request = $this->adminRequest([
            'username' => 'promotor01',
            'password' => 'secure-password-123',
            'role' => 'admin',
        ]);

        $this->expectException(ValidationException::class);

        (new PromoterController())->store($request);
    }

    public function test_promoter_creation_rejects_duplicate_username(): void
    {
        $query = Mockery::mock();

        $query->shouldReceive('where')
            ->once()
            ->with('username', 'promotor01')
            ->andReturnSelf();

        $query->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        DB::shouldReceive('table')
            ->once()
            ->with('promoters')
            ->andReturn($query);

        $request = $this->adminRequest([
            'username' => 'PROMOTOR01',
            'password' => 'secure-password-123',
            'role' => 'promoter',
        ]);

        $this->expectException(ApiException::class);

        (new PromoterController())->store($request);
    }

    public function test_admin_can_create_promoter(): void
    {
        $query = Mockery::mock();

        $query->shouldReceive('where')
            ->once()
            ->with('username', 'promotor01')
            ->andReturnSelf();

        $query->shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $query->shouldReceive('insert')
            ->once()
            ->with(Mockery::on(function ($data) {
                return isset($data['id'])
                    && $data['username'] === 'promotor01'
                    && $data['role'] === 'promoter'
                    && $data['password_hash'] === 'hashed-password';
            }))
            ->andReturn(true);

        DB::shouldReceive('table')
            ->twice()
            ->with('promoters')
            ->andReturn($query);

        Hash::shouldReceive('make')
            ->once()
            ->with('secure-password-123')
            ->andReturn('hashed-password');

        $request = $this->adminRequest([
            'username' => 'PROMOTOR01',
            'password' => 'secure-password-123',
            'role' => 'promoter',
        ]);

        $response = (new PromoterController())->store($request);

        $this->assertSame(201, $response->getStatusCode());

        $data = $response->getData(true);

        $this->assertSame('promotor01', $data['username']);
        $this->assertSame('promoter', $data['role']);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayNotHasKey('password', $data);
        $this->assertArrayNotHasKey('password_hash', $data);
    }
}
