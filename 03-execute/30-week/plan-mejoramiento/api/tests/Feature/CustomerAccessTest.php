<?php
namespace Tests\Feature;

use App\Support\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class CustomerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_catalog_but_cannot_access_staff_operations(): void
    {
        $admin = '70000000-0000-4000-8000-000000000001';
        DB::table('promoters')->insert(['id' => $admin, 'username' => 'admin_customer_test', 'password_hash' => Hash::make('password-12345'), 'role' => 'admin']);
        $adminHeaders = ['Authorization' => 'Bearer ' . app(JwtService::class)->issue($admin, 'admin')];
        $this->postJson('/api/v1/users', ['username' => 'cliente_test', 'password' => 'password-12345', 'role' => 'customer'], $adminHeaders)
            ->assertCreated()->assertJsonPath('role', 'customer');
        $login = $this->postJson('/api/v1/auth/login', ['username' => 'cliente_test', 'password' => 'password-12345'])->assertOk()->assertJsonPath('role', 'customer')->json();
        $headers = ['Authorization' => 'Bearer ' . $login['access_token']];
        $this->getJson('/api/v1/categories', $headers)->assertOk();
        $this->getJson('/api/v1/toys', $headers)->assertOk();
        foreach (['/families', '/loans', '/loans/overdue', '/reports/most-loaned'] as $path) {
            $this->getJson('/api/v1' . $path, $headers)->assertForbidden()->assertJsonPath('code', 'FORBIDDEN_ROLE');
        }
        foreach (['/families', '/loans', '/toys', '/users'] as $path) {
            $this->postJson('/api/v1' . $path, [], $headers)->assertForbidden();
        }
    }
}
