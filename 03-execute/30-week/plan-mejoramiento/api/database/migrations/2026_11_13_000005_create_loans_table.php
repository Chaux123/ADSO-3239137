
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('family_id');
            $table->uuid('loaned_by_promoter_id');

            $table->timestampTz('loaned_at');

            $table->foreign('family_id')
                ->references('id')
                ->on('families')
                ->restrictOnDelete();

            $table->foreign('loaned_by_promoter_id')
                ->references('id')
                ->on('promoters')
                ->restrictOnDelete();

            $table->index('family_id');
            $table->index('loaned_by_promoter_id');
            $table->index('loaned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
