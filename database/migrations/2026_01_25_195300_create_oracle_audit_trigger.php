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
        // Crear tabla de auditoría para clientes
        DB::statement("
            CREATE TABLE log_clientes (
                LOG_ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
                CLI_CED_RUC VARCHAR2(13) NOT NULL,
                LOG_CAMPO VARCHAR2(50) NOT NULL,
                LOG_VALOR_ANTERIOR VARCHAR2(500),
                LOG_VALOR_NUEVO VARCHAR2(500),
                LOG_FECHA TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                LOG_USUARIO VARCHAR2(100),
                CONSTRAINT fk_log_cliente FOREIGN KEY (CLI_CED_RUC) 
                    REFERENCES CLIENTES(CLI_CED_RUC) ON DELETE CASCADE
            )
        ");

        // Crear índices para optimizar consultas
        DB::statement("CREATE INDEX idx_log_clientes_cedula ON log_clientes(CLI_CED_RUC)");
        DB::statement("CREATE INDEX idx_log_clientes_fecha ON log_clientes(LOG_FECHA)");

        // NOTA: El trigger debe ser creado manualmente en SQL*Plus o SQL Developer
        // Ver: database/oracle/MANUAL_INSTALLATION.md
        // Razón: Laravel tiene problemas ejecutando triggers complejos en Oracle

        /*
        // Leer y ejecutar el trigger desde archivo SQL
        $triggerSql = file_get_contents(database_path('oracle/trg_auditoria_clientes.sql'));
        DB::statement($triggerSql);
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER trg_auditoria_clientes');
        DB::statement('DROP TABLE log_clientes CASCADE CONSTRAINTS');
    }
};
