
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promoters', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('username', 120)->unique();

            $table->string('password_hash', 255);

            $table->string('role', 20);
        });

        DB::statement("
            ALTER TABLE promoters
            ADD CONSTRAINT promoters_role_check
            CHECK (role IN ('admin', 'promoter'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('promoters');
    }
};
