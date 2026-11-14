
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('loan_id');
            $table->uuid('toy_id');

            // Historical toy snapshot.
            $table->string('toy_name', 250);
            $table->string('reference', 50);
            $table->string('category_name', 120);

            // Loan and return dates in UTC.
            $table->timestampTz('due_at');
            $table->timestampTz('returned_at')->nullable();

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->restrictOnDelete();

            $table->foreign('toy_id')
                ->references('id')
                ->on('toys')
                ->restrictOnDelete();

            // A toy cannot appear twice in the same loan.
            $table->unique(['loan_id', 'toy_id']);

            $table->index('toy_id');
        });

        DB::statement("
            CREATE INDEX ix_open_loan_due
            ON loan_items (due_at)
            WHERE returned_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
