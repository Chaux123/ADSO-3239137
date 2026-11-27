<?php
namespace Tests\Feature;

use App\Support\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class FamilyLoanLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_limit_counts_all_pending_loans_and_return_frees_capacity(): void
    {
        $category = '50000000-0000-4000-8000-000000000001';
        $promoter = '50000000-0000-4000-8000-000000000002';
        $family = '50000000-0000-4000-8000-000000000003';
        DB::table('categories')->insert(['id' => $category, 'name' => 'Prueba']);
        DB::table('promoters')->insert(['id' => $promoter, 'username' => 'limit_test', 'password_hash' => Hash::make('test-password'), 'role' => 'promoter']);
        DB::table('families')->insert(['id' => $family, 'full_name' => 'Familia prueba', 'document_number' => 'LIMIT-01']);
        $toys = [];
        for ($i = 4; $i <= 7; $i++) {
            $id = sprintf('50000000-0000-4000-8000-%012d', $i);
            DB::table('toys')->insert(['id' => $id, 'name' => 'Prueba', 'brand' => 'Prueba', 'reference' => 'LIMIT-0' . $i, 'category_id' => $category, 'total_units' => 1, 'available_units' => 1]);
            $toys[] = $id;
        }
        $headers = ['Authorization' => 'Bearer ' . app(JwtService::class)->issue($promoter, 'promoter')];
        $first = $this->postJson('/api/v1/loans', ['family_id' => $family, 'toy_ids' => [$toys[0]]], $headers)->assertCreated()->json();
        $this->postJson('/api/v1/loans', ['family_id' => $family, 'toy_ids' => [$toys[1], $toys[2]]], $headers)->assertCreated();
        $this->postJson('/api/v1/loans', ['family_id' => $family, 'toy_ids' => [$toys[3]]], $headers)->assertStatus(422);
        $this->assertSame(1, (int) DB::table('toys')->where('id', $toys[3])->value('available_units'));
        $this->postJson('/api/v1/loans/' . $first['id'] . '/items/' . $first['items'][0]['id'] . '/return', [], $headers)->assertOk();
        $this->postJson('/api/v1/loans', ['family_id' => $family, 'toy_ids' => [$toys[3]]], $headers)->assertCreated();
    }
}
