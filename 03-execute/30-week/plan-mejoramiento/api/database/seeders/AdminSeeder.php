<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = strtolower(trim((string) env('INITIAL_ADMIN_USERNAME', 'admin')));
        $password = (string) env('INITIAL_ADMIN_PASSWORD', '');
        if ($username === '' || strlen($password) < 8) {
            throw new RuntimeException('Configure INITIAL_ADMIN_USERNAME y una contraseña INITIAL_ADMIN_PASSWORD de al menos 8 caracteres.');
        }
        if (!DB::table('promoters')->where('username', $username)->exists()) {
            DB::table('promoters')->insert([
                'id' => (string) Str::uuid(),
                'username' => $username,
                'password_hash' => Hash::make($password),
                'role' => 'admin',
            ]);
        }
    }
}
