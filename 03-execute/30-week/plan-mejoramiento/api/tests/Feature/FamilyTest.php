<?php

namespace Tests\Feature;

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

final class FamilyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Authentication is tested separately.
        $this->withoutMiddleware(JwtMiddleware::class);
    }

    public function test_family_registration_requires_full_name(): void
    {
        $response = $this->postJson('/api/v1/families', [
            'document_number' => '123456789',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'full_name',
        ]);
    }

    public function test_family_registration_requires_document_number(): void
    {
        $response = $this->postJson('/api/v1/families', [
            'full_name' => 'Familia Chaux',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'document_number',
        ]);
    }

    public function test_family_registration_rejects_empty_fields(): void
    {
        $response = $this->postJson('/api/v1/families', [
            'full_name' => '',
            'document_number' => '',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'full_name',
            'document_number',
        ]);
    }

    public function test_family_registration_rejects_long_name(): void
    {
        $response = $this->postJson('/api/v1/families', [
            'full_name' => str_repeat('A', 201),
            'document_number' => '123456789',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'full_name',
        ]);
    }

    public function test_family_registration_rejects_long_document(): void
    {
        $response = $this->postJson('/api/v1/families', [
            'full_name' => 'Familia Chaux',
            'document_number' => str_repeat('1', 31),
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'document_number',
        ]);
    }

    public function test_family_search_rejects_long_query(): void
    {
        $query = str_repeat('A', 201);

        $response = $this->getJson(
            '/api/v1/families?q=' . $query
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'q',
        ]);
    }

    public function test_family_registration_rejects_duplicate_document(): void
    {
        $query = Mockery::mock();

        $query->shouldReceive('where')
            ->once()
            ->with('document_number', '123456789')
            ->andReturnSelf();

        $query->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        DB::shouldReceive('table')
            ->once()
            ->with('families')
            ->andReturn($query);

        $response = $this->postJson('/api/v1/families', [
            'full_name' => 'Familia Chaux',
            'document_number' => '123456789',
        ]);

        $response->assertStatus(409);
    }

    public function test_family_registration_succeeds(): void
    {
        $query = Mockery::mock();

        $query->shouldReceive('where')
            ->once()
            ->with('document_number', '123456789')
            ->andReturnSelf();

        $query->shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $query->shouldReceive('insert')
            ->once()
            ->andReturn(true);

        DB::shouldReceive('table')
            ->twice()
            ->with('families')
            ->andReturn($query);

        $response = $this->postJson('/api/v1/families', [
            'full_name' => 'Familia Chaux',
            'document_number' => '123456789',
        ]);

        $response->assertStatus(201);

        $response->assertJson([
            'full_name' => 'Familia Chaux',
            'document_number' => '123456789',
        ]);

        $response->assertJsonStructure([
            'id',
            'full_name',
            'document_number',
        ]);
    }

    public function test_family_details_returns_not_found(): void
    {
        $query = Mockery::mock();

        $query->shouldReceive('where')
            ->once()
            ->with('id', 'nonexistent-family')
            ->andReturnSelf();

        $query->shouldReceive('first')
            ->once()
            ->andReturn(null);

        DB::shouldReceive('table')
            ->once()
            ->with('families')
            ->andReturn($query);

        $response = $this->getJson(
            '/api/v1/families/nonexistent-family'
        );

        $response->assertStatus(404);
    }
}
