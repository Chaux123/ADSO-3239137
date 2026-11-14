
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'bebés',
            'construcción',
            'didácticos',
            'exterior',
            'simbólicos',
        ];

        foreach ($categories as $name) {
            if (!DB::table('categories')
                ->where('name', $name)
                ->exists()) {
                DB::table('categories')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $name,
                ]);
            }
        }
    }
}
