<?php

namespace Tests\Feature;

use App\Support\JwtService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Process;
use Tests\TestCase;

final class SimultaneousLoanTest extends TestCase
{
    use DatabaseMigrations;

    public function test_two_simultaneous_requests_for_the_last_unit(): void
    {
        $category = '10000000-0000-4000-8000-000000000001';
        $toy = '10000000-0000-4000-8000-000000000002';
        $promoter = '10000000-0000-4000-8000-000000000003';
        $families = ['10000000-0000-4000-8000-000000000004', '10000000-0000-4000-8000-000000000005'];
        DB::table('categories')->insert(['id' => $category, 'name' => 'Prueba']);
        DB::table('toys')->insert(['id' => $toy, 'name' => 'Prueba', 'brand' => 'Prueba', 'reference' => 'RACE-001', 'category_id' => $category, 'total_units' => 1, 'available_units' => 1]);
        DB::table('promoters')->insert(['id' => $promoter, 'username' => 'race_test', 'password_hash' => Hash::make('test-password'), 'role' => 'promoter']);
        foreach ($families as $i => $id) {
            DB::table('families')->insert(['id' => $id, 'full_name' => 'Familia prueba', 'document_number' => 'RACE-' . $i]);
        }
        $token = app(JwtService::class)->issue($promoter, 'promoter');
        $env = ['APP_ENV' => 'testing', 'DB_CONNECTION' => 'pgsql', 'DB_DATABASE' => config('database.connections.pgsql.database'), 'JWT_SECRET' => config('services.jwt.secret')];
        $processes = [];
        DB::beginTransaction();
        try {
            DB::table('toys')->where('id', $toy)->lockForUpdate()->first();
            foreach ($families as $family) {
                $process = new Process([PHP_BINARY, base_path('tests/Support/concurrent-loan-worker.php'), $token, $family, $toy], base_path(), $env);
                $process->setTimeout(30);
                $process->start();
                $processes[] = $process;
            }
            $deadline = microtime(true) + 15;
            do {
                DB::select('SELECT pg_stat_clear_snapshot()');
                $waiting = DB::selectOne("SELECT count(*) AS total FROM pg_stat_activity WHERE datname = current_database() AND wait_event_type = 'Lock' AND query ILIKE '%toys%' AND pid <> pg_backend_pid()")->total;
                if ($waiting >= 2) break;
                usleep(100000);
            } while (microtime(true) < $deadline);
            $this->assertGreaterThanOrEqual(2, $waiting, 'Ambas solicitudes deben esperar el mismo bloqueo de PostgreSQL. ' . implode(' | ', array_map(fn ($p) => $p->getOutput() . $p->getErrorOutput(), $processes)));
        } finally {
            DB::rollBack();
        }
        $statuses = [];
        foreach ($processes as $process) {
            $process->wait();
            $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
            $statuses[] = (int) trim($process->getOutput());
        }
        sort($statuses);
        $this->assertSame([201, 409], $statuses);
        $this->assertSame(0, (int) DB::table('toys')->where('id', $toy)->value('available_units'));
        $this->assertSame(1, DB::table('loan_items')->where('toy_id', $toy)->count());
    }
}
