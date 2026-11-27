<?php

namespace Tests\Feature;

use App\Support\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class CoverUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_browser_multipart_upload_and_delete_cover(): void
    {
        Storage::fake('local');
        $category = '30000000-0000-4000-8000-000000000001';
        $toy = '30000000-0000-4000-8000-000000000002';
        $admin = '30000000-0000-4000-8000-000000000003';
        DB::table('categories')->insert(['id' => $category, 'name' => 'Prueba']);
        DB::table('toys')->insert(['id' => $toy, 'name' => 'Prueba', 'brand' => 'Prueba', 'reference' => 'IMG-001', 'category_id' => $category, 'total_units' => 1, 'available_units' => 1]);
        DB::table('promoters')->insert(['id' => $admin, 'username' => 'image_admin', 'password_hash' => Hash::make('test-password'), 'role' => 'admin']);
        $headers = ['Authorization' => 'Bearer ' . app(JwtService::class)->issue($admin, 'admin'), 'Accept' => 'application/json'];
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
        $file = UploadedFile::fake()->createWithContent('toy.png', $png);
        $first = $this->post('/api/v1/toys/' . $toy . '/cover', ['_method' => 'PUT', 'file' => $file], $headers)->assertOk()->json('cover_key');
        $secondFile = UploadedFile::fake()->createWithContent('new.png', $png);
        $second = $this->post('/api/v1/toys/' . $toy . '/cover', ['_method' => 'PUT', 'file' => $secondFile], $headers)->assertOk()->json('cover_key');
        $this->assertNotSame($first, $second);
        Storage::disk('local')->assertMissing('covers/' . $first);
        Storage::disk('local')->assertExists('covers/' . $second);
        $boundary = UploadedFile::fake()->createWithContent('boundary.png', $png . str_repeat('x', 2 * 1024 * 1024 - strlen($png)));
        $second = $this->post('/api/v1/toys/' . $toy . '/cover', ['_method' => 'PUT', 'file' => $boundary], $headers)->assertOk()->json('cover_key');
        $tooLarge = UploadedFile::fake()->createWithContent('large.png', $png . str_repeat('x', 3 * 1024 * 1024));
        $this->post('/api/v1/toys/' . $toy . '/cover', ['_method' => 'PUT', 'file' => $tooLarge], $headers)->assertStatus(422);
        $wrongType = UploadedFile::fake()->createWithContent('file.txt', 'not an image');
        $this->post('/api/v1/toys/' . $toy . '/cover', ['_method' => 'PUT', 'file' => $wrongType], $headers)->assertStatus(422);
        $this->assertSame($second, DB::table('toys')->where('id', $toy)->value('cover_key'));
        $this->get('/api/v1/toys/' . $toy . '/cover', $headers)->assertOk()->assertHeader('content-type', 'image/png');
        $jpg = new UploadedFile(__DIR__ . '/../Fixtures/white.jpg', 'white.jpg', 'image/jpeg', null, true);
        $this->post('/api/v1/toys/' . $toy . '/cover', ['_method' => 'PUT', 'file' => $jpg], $headers)->assertOk();
        $this->get('/api/v1/toys/' . $toy . '/cover', $headers)->assertOk()->assertHeader('content-type', 'image/jpeg');
        $this->deleteJson('/api/v1/toys/' . $toy . '/cover', [], $headers)->assertNoContent();
        $this->getJson('/api/v1/toys/' . $toy . '/cover', $headers)->assertNotFound();
    }
}
