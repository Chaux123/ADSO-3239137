<?php
namespace Tests\Feature;

use App\Support\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthenticationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_promoter_with_hashed_password_and_duplicate_is_rejected(): void
    {
        $admin = '60000000-0000-4000-8000-000000000002';
        DB::table('promoters')->insert(['id' => $admin, 'username' => 'admin_security', 'password_hash' => Hash::make('admin-password-123'), 'role' => 'admin']);
        $headers = ['Authorization' => 'Bearer ' . app(JwtService::class)->issue($admin, 'admin')];
        $payload = ['username' => ' Nuevo_Promotor ', 'password' => 'promoter-password-123', 'role' => 'promoter'];
        $created = $this->postJson('/api/v1/users', $payload, $headers)->assertCreated()->assertJsonPath('username', 'nuevo_promotor')->json();
        $this->assertArrayNotHasKey('password_hash', $created);
        $hash = DB::table('promoters')->where('id', $created['id'])->value('password_hash');
        $this->assertNotSame($payload['password'], $hash);
        $this->assertTrue(Hash::check($payload['password'], $hash));
        $this->postJson('/api/v1/users', $payload, $headers)->assertStatus(409)->assertJsonPath('code', 'USERNAME_TAKEN');
        $promoterHeaders = ['Authorization' => 'Bearer ' . app(JwtService::class)->issue($created['id'], 'promoter')];
        $this->postJson('/api/v1/users', $payload, $promoterHeaders)->assertForbidden();
    }

    public function test_login_and_authorization_use_current_database_role(): void
    {
        $id = '60000000-0000-4000-8000-000000000001';
        DB::table('promoters')->insert(['id' => $id, 'username' => 'security_test', 'password_hash' => Hash::make('secure-password-123'), 'role' => 'promoter']);
        $login = $this->postJson('/api/v1/auth/login', ['username' => ' SECURITY_TEST ', 'password' => 'secure-password-123'])->assertOk()->assertJsonPath('role', 'promoter')->json();
        $this->assertSame(28800, $login['expires_in']);
        $this->assertArrayNotHasKey('password', $login);
        $this->assertArrayNotHasKey('password_hash', $login);
        $headers = ['Authorization' => 'Bearer ' . $login['access_token']];
        $this->getJson('/api/v1/families', $headers)->assertOk();
        $this->getJson('/api/v1/reports/most-loaned?from=2000-01-01&to=2099-12-31', $headers)->assertForbidden();
        $this->postJson('/api/v1/auth/login', ['username' => 'security_test', 'password' => 'wrong-password'])->assertUnauthorized()->assertJsonPath('code', 'INVALID_CREDENTIALS');
        $this->getJson('/api/v1/families', ['Authorization' => 'Bearer ' . $login['access_token'] . 'invalid'])->assertUnauthorized();
        $forgedRole = app(JwtService::class)->issue($id, 'admin');
        $this->getJson('/api/v1/reports/most-loaned?from=2000-01-01&to=2099-12-31', ['Authorization' => 'Bearer ' . $forgedRole])->assertForbidden();
    }

    public function test_expired_signed_token_is_rejected(): void
    {
        $encode = fn ($value) => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
        $header = $encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = $encode(json_encode(['sub' => 'expired-user', 'role' => 'promoter', 'iat' => time() - 100, 'exp' => time() - 1]));
        $message = $header . '.' . $payload;
        $signature = $encode(hash_hmac('sha256', $message, config('services.jwt.secret'), true));
        $this->getJson('/api/v1/families', ['Authorization' => 'Bearer ' . $message . '.' . $signature])
            ->assertUnauthorized()->assertJsonPath('code', 'INVALID_TOKEN');
    }
}
