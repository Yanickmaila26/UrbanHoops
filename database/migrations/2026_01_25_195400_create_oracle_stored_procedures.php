<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NOTA: El procedimiento debe ser creado manualmente en SQL*Plus o SQL Developer
        // Ver: database/oracle/MANUAL_INSTALLATION.md
        // Razón: Laravel tiene problemas ejecutando procedimientos complejos en Oracle

        // El SQL completo está en: database/oracle/sp_finalizar_compra_web.sql

        /*
        DB::statement("...procedimiento...");
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE sp_finalizar_compra_web');
    }
};
