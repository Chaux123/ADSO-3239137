<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE promoters DROP CONSTRAINT promoters_role_check');
        DB::statement("ALTER TABLE promoters ADD CONSTRAINT promoters_role_check CHECK (role IN ('admin', 'promoter', 'customer'))");
    }

    public function down(): void
    {
        if (DB::table('promoters')->where('role', 'customer')->exists()) {
            throw new RuntimeException('No se puede revertir la migración mientras existan cuentas de clientes.');
        }
        DB::statement('ALTER TABLE promoters DROP CONSTRAINT promoters_role_check');
        DB::statement("ALTER TABLE promoters ADD CONSTRAINT promoters_role_check CHECK (role IN ('admin', 'promoter'))");
    }
};
