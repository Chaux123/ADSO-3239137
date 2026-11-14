
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('family_id');
            $table->uuid('promoter_id');

            $table->timestampTz('created_at');
            $table->timestampTz('due_at');

            $table->foreign('family_id')
                ->references('id')
                ->on('families')
                ->restrictOnDelete();

            $table->foreign('promoter_id')
                ->references('id')
                ->on('promoters')
                ->restrictOnDelete();

            $table->index('family_id');
            $table->index('promoter_id');
            $table->index('due_at');
        });

        DB::statement("
            ALTER TABLE loans
            ADD CONSTRAINT loans_due_date_check
            CHECK (due_at > created_at)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
