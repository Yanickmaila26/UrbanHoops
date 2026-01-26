<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = database_path('oracle/sp_emitir_factura.sql');

        if (File::exists($path)) {
            $sql = File::get($path);

            // Remove checks/comments that might interfere
            // Remove trailing slash usually found in Oracle scripts
            $sql = preg_replace('/^\/$/m', '', $sql);

            try {
                DB::unprepared($sql);
            } catch (\Exception $e) {
            }
        }

        // También actualizar sp_finalizar_compra_web que tenía problemas de casing
        $path2 = database_path('oracle/sp_finalizar_compra_web.sql');
        if (File::exists($path2)) {
            $sql2 = File::get($path2);
            $sql2 = preg_replace('/^\/$/m', '', $sql2);
            try {
                DB::unprepared($sql2);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE sp_emitir_factura');
        try {
            DB::statement('DROP PROCEDURE sp_finalizar_compra_web');
        } catch (\Exception $e) {
        }
    }
};
