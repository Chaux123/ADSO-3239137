<?php

namespace Tests\Feature;

use App\Support\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoanLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_return_inventory_and_historical_snapshot(): void
    {
        $category = '20000000-0000-4000-8000-000000000001';
        $admin = '20000000-0000-4000-8000-000000000002';
        DB::table('categories')->insert(['id' => $category, 'name' => 'Construcción']);
        DB::table('promoters')->insert(['id' => $admin, 'username' => 'admin_test', 'password_hash' => Hash::make('test-password'), 'role' => 'admin']);
        $headers = ['Authorization' => 'Bearer ' . app(JwtService::class)->issue($admin, 'admin')];
        $family = $this->postJson('/api/v1/families', ['full_name' => 'Familia prueba', 'document_number' => 'LIFE-001'], $headers)->assertCreated()->json();
        $payload = ['name' => 'Bloques originales', 'brand' => 'Prueba', 'reference' => 'LIFE-001', 'category_id' => $category, 'total_units' => 1];
        $toy = $this->postJson('/api/v1/toys', $payload, $headers)->assertCreated()->json();
        $this->postJson('/api/v1/loans', ['family_id' => $family['id'], 'toy_ids' => [$toy['id'], $toy['id']]], $headers)
            ->assertStatus(422)->assertJsonPath('code', 'DUPLICATE_TOY_IN_LOAN')
            ->assertJsonPath('details.0.issue', 'No se puede repetir el mismo valor.');
        $loan = $this->postJson('/api/v1/loans', ['family_id' => $family['id'], 'items' => [['toy_id' => $toy['id']]]], $headers)->assertCreated()->json();
        $this->assertSame(0, (int) DB::table('toys')->where('id', $toy['id'])->value('available_units'));
        $this->assertSame($category, $toy['category']['id']);
        $this->getJson('/api/v1/families/' . $family['id'], $headers)->assertOk()->assertJsonCount(1, 'active_loans');
        $this->assertSame('open', $loan['status']);
        $this->assertSame('Bloques originales', $loan['items'][0]['toy_name']);
        $this->getJson('/api/v1/loans?size=1&status=open', $headers)->assertOk()->assertJsonPath('size', 1)->assertJsonPath('total', 1)->assertJsonCount(1, 'items');
        $payload['name'] = 'Bloques nuevos';
        $this->putJson('/api/v1/toys/' . $toy['id'], $payload, $headers)->assertOk();
        $this->getJson('/api/v1/loans/' . $loan['id'], $headers)->assertOk()->assertJsonPath('items.0.snapshot.toy_name', 'Bloques originales');
        $this->travel(11)->days();
        $this->getJson('/api/v1/loans/overdue', $headers)->assertOk()->assertJsonPath('0.loan_id', $loan['id'])->assertJsonPath('0.days_late', 2);
        $this->postJson('/api/v1/loans', ['family_id' => $family['id'], 'toy_ids' => [$toy['id']]], $headers)->assertStatus(409);
        $this->travelBack();
        $this->deleteJson('/api/v1/toys/' . $toy['id'], [], $headers)->assertStatus(409)->assertJsonPath('code', 'TOY_HAS_OPEN_LOANS');
        $returnUrl = '/api/v1/loans/' . $loan['id'] . '/items/' . $loan['items'][0]['id'] . '/return';
        $this->postJson($returnUrl, [], $headers)->assertOk();
        $this->postJson($returnUrl, [], $headers)->assertStatus(409);
        $this->assertSame(1, (int) DB::table('toys')->where('id', $toy['id'])->value('available_units'));
        $this->getJson('/api/v1/toys?q=Bloques%20nuevos', $headers)->assertOk()->assertJsonPath('total', 1);
        $this->deleteJson('/api/v1/toys/' . $toy['id'], [], $headers)->assertNoContent();
        $this->getJson('/api/v1/toys/' . $toy['id'], $headers)->assertNotFound();
        $this->getJson('/api/v1/toys?q=Bloques%20nuevos', $headers)->assertOk()->assertJsonPath('total', 0);
        $this->getJson('/api/v1/reports/most-loaned?from=2000-01-01&to=2099-12-31', $headers)->assertOk()->assertJsonPath('data.0.name', 'Bloques originales')->assertJsonPath('data.0.total_loans', 1)->assertJsonPath('rows.0.toy_name', 'Bloques originales')->assertJsonPath('rows.0.category_name', 'Construcción');
        $this->getJson('/api/v1/reports/most-loaned?from=2000-01-01&to=2000-01-02', $headers)->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/reports/most-loaned?from=2026-02-01&to=2026-01-01', $headers)->assertStatus(422)->assertJsonPath('code', 'INVALID_DATE_RANGE');
        DB::table('loans')->where('id', $loan['id'])->update(['loaned_at' => '2026-01-01 00:00:00+00']);
        $this->getJson('/api/v1/reports/most-loaned?from=2026-01-01&to=2026-01-31', $headers)->assertOk()->assertJsonCount(1, 'rows');
        DB::table('loans')->where('id', $loan['id'])->update(['loaned_at' => '2026-01-31 23:59:59+00']);
        $this->getJson('/api/v1/reports/most-loaned?from=2026-01-01&to=2026-01-31', $headers)->assertOk()->assertJsonCount(1, 'rows');
        $this->getJson('/api/v1/loans?from=2026-01-01&to=2026-01-31&status=closed', $headers)->assertOk()->assertJsonCount(1, 'items');
    }
}
