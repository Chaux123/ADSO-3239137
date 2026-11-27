<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE toys ADD CONSTRAINT toys_available_nonnegative CHECK (available_units >= 0)');
        DB::statement('ALTER TABLE toys DROP CONSTRAINT toys_reference_unique');
        DB::statement('CREATE UNIQUE INDEX toys_active_reference_unique ON toys (reference) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX toys_active_reference_unique');
        DB::statement('ALTER TABLE toys ADD CONSTRAINT toys_reference_unique UNIQUE (reference)');
        DB::statement('ALTER TABLE toys DROP CONSTRAINT toys_available_nonnegative');
    }
};
