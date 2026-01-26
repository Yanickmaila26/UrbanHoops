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

        // Install sp_registrar_ingreso_bodega (Updated: includes explicit Kardex insert)
        $path3 = database_path('oracle/sp_registrar_ingreso_bodega.sql');
        if (File::exists($path3)) {
            $sql3 = preg_replace('/^\/$/m', '', File::get($path3));
            try {
                DB::unprepared($sql3);
            } catch (\Exception $e) {
            }
        }

        // Install trg_bloqueo_facturas (Updated: fixed casing)
        $path4 = database_path('oracle/trg_bloqueo_facturas.sql');
        if (File::exists($path4)) {
            $sql4 = preg_replace('/^\/$/m', '', File::get($path4));
            try {
                DB::unprepared($sql4);
            } catch (\Exception $e) {
            }
        }

        // Install trg_auditoria_clientes (Updated: fixed casing)
        $path5 = database_path('oracle/trg_auditoria_clientes.sql');
        if (File::exists($path5)) {
            $sql5 = preg_replace('/^\/$/m', '', File::get($path5));
            try {
                DB::unprepared($sql5);
            } catch (\Exception $e) {
            }
        }

        // Eliminar trigger conflictivo TRG_MOVIMIENTO_KARDEX (Reemplazado por lógica en SPs y PHP)
        try {
            DB::statement("DROP TRIGGER trg_kardex_producto_bodega");
        } catch (\Exception $e) {
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
