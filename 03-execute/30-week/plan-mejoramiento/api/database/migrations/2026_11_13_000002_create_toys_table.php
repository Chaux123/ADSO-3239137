
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toys', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name', 250);
            $table->string('brand', 200);
            $table->string('reference', 50)->unique();

            $table->uuid('category_id');

            $table->unsignedInteger('total_units');
            $table->unsignedInteger('available_units');

            $table->string('cover_key')->nullable();
            $table->timestampTz('deleted_at')->nullable();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->restrictOnDelete();
        });

        DB::statement('
            ALTER TABLE toys
            ADD CONSTRAINT toys_inventory_check
            CHECK (
                total_units >= 1
                AND available_units <= total_units
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('toys');
    }
};
