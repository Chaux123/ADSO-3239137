<?php
namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class InventoryConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_rejects_negative_inventory(): void
    {
        $category = '40000000-0000-4000-8000-000000000001';
        DB::table('categories')->insert(['id' => $category, 'name' => 'Prueba']);
        $this->expectException(QueryException::class);
        DB::table('toys')->insert(['id' => '40000000-0000-4000-8000-000000000002', 'name' => 'Prueba', 'brand' => 'Prueba', 'reference' => 'NEG-01', 'category_id' => $category, 'total_units' => 1, 'available_units' => -1]);
    }

    public function test_reference_can_be_reused_after_logical_deletion(): void
    {
        $category = '40000000-0000-4000-8000-000000000001';
        DB::table('categories')->insert(['id' => $category, 'name' => 'Prueba']);
        $toy = ['name' => 'Prueba', 'brand' => 'Prueba', 'reference' => 'REUSE-01', 'category_id' => $category, 'total_units' => 1, 'available_units' => 1];
        DB::table('toys')->insert($toy + ['id' => '40000000-0000-4000-8000-000000000002', 'deleted_at' => now()]);
        DB::table('toys')->insert($toy + ['id' => '40000000-0000-4000-8000-000000000003']);
        $this->assertSame(2, DB::table('toys')->where('reference', 'REUSE-01')->count());
    }
    public function test_database_rejects_invalid_totals_roles_relations_and_duplicate_references(): void
    {
        $category = '40000000-0000-4000-8000-000000000001';
        DB::table('categories')->insert(['id' => $category, 'name' => 'Prueba']);
        $toy = ['id' => '40000000-0000-4000-8000-000000000002', 'name' => 'Prueba', 'brand' => 'Prueba', 'reference' => 'CHECK-01', 'category_id' => $category, 'total_units' => 1, 'available_units' => 1];
        $this->assertRejected(fn () => DB::table('toys')->insert(array_replace($toy, ['total_units' => 0, 'available_units' => 0])));
        $this->assertRejected(fn () => DB::table('toys')->insert(array_replace($toy, ['available_units' => 2])));
        $this->assertRejected(fn () => DB::table('toys')->insert(array_replace($toy, ['category_id' => '40000000-0000-4000-8000-000000000099'])));
        $this->assertRejected(fn () => DB::table('promoters')->insert(['id' => '40000000-0000-4000-8000-000000000003', 'username' => 'invalid_role', 'password_hash' => 'hash', 'role' => 'other']));
        DB::table('toys')->insert($toy);
        $this->assertRejected(fn () => DB::table('toys')->insert(array_replace($toy, ['id' => '40000000-0000-4000-8000-000000000004'])));
    }

    private function assertRejected(callable $operation): void
    {
        try {
            DB::transaction($operation);
            $this->fail('PostgreSQL debía rechazar el registro inválido.');
        } catch (QueryException $exception) {
            $this->assertContains((string) $exception->getCode(), ['23503', '23505', '23514']);
        }
    }
}
