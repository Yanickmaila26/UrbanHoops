<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DistributeOracleDatabase extends Command
{
    protected $signature = 'oracle:distribute {--skip-confirmation : Skip all confirmation prompts}';
    protected $description = 'Configura la base de datos distribuida entre PROD y COMEE';

    private $prodConnection = 'oracle';
    private $comeeConnection = 'oracle_comee';

    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║   UrbanHoops - Distributed Database Setup                     ║');
        $this->info('║   Configuración de Base de Datos Distribuida                  ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Verificar prerequisitos
        if (!$this->verifyPrerequisites()) {
            return 1;
        }

        if (!$this->option('skip-confirmation')) {
            if (!$this->confirm('¿Continuar con la configuración distribuida?', true)) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        try {
            // Fase 1: Setup COMEE
            $this->executePhase1();

            // Fase 2: Setup PROD
            $this->executePhase2();

            // Fase 3: Completar COMEE
            $this->executePhase3();

            // Fase 4: Migrar Datos
            $this->executePhase4();

            // Fase 5: Verificación
            $this->executePhase5();

            $this->newLine();
            $this->info('╔════════════════════════════════════════════════════════════════╗');
            $this->info('║   ✅ Configuración Distribuida Completada!                     ║');
            $this->info('╚════════════════════════════════════════════════════════════════╝');
            $this->newLine();
            $this->info('Próximos pasos:');
            $this->info('  1. Ejecuta: php artisan migrate:fresh-oracle --seed');
            $this->info('  2. Verifica que las tablas están en las bases de datos correctas');
            $this->newLine();

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function verifyPrerequisites()
    {
        $this->info('🔍 Verificando prerequisitos...');
        $this->newLine();

        // Test PROD connection
        try {
            DB::connection($this->prodConnection)->select('SELECT 1 FROM dual');
            $this->info('✓ Conexión a PROD exitosa');
        } catch (\Exception $e) {
            $this->error('✗ No se puede conectar a PROD: ' . $e->getMessage());
            return false;
        }

        // Test COMEE connection
        try {
            DB::connection($this->comeeConnection)->select('SELECT 1 FROM dual');
            $this->info('✓ Conexión a COMEE exitosa');
        } catch (\Exception $e) {
            $this->error('✗ No se puede conectar a COMEE: ' . $e->getMessage());
            $this->warn('Verifica la configuración en .env:');
            $this->warn('  DB_HOST_COMEE=172.16.18.125');
            $this->warn('  DB_SERVICE_NAME_COMEE=comee');
            $this->warn('  DB_USERNAME_COMEE=u_comee');
            $this->warn('  DB_PASSWORD_COMEE=secreto123');
            return false;
        }

        // Test database link PROD -> COMEE
        try {
            DB::connection($this->prodConnection)->select('SELECT 1 FROM dual@link_comee');
            $this->info('✓ Database link PROD -> COMEE funcionando');
        } catch (\Exception $e) {
            $this->error('✗ Database link link_comee no funciona: ' . $e->getMessage());
            $this->warn('Crea el database link en PROD:');
            $this->warn('  CREATE DATABASE LINK link_comee CONNECT TO u_comee IDENTIFIED BY secreto123 USING \'comee\';');
            return false;
        }

        // Test database link COMEE -> PROD
        try {
            DB::connection($this->comeeConnection)->select('SELECT 1 FROM dual@link_prod');
            $this->info('✓ Database link COMEE -> PROD funcionando');
        } catch (\Exception $e) {
            $this->error('✗ Database link link_prod no funciona: ' . $e->getMessage());
            $this->warn('Crea el database link en COMEE:');
            $this->warn('  CREATE DATABASE LINK link_prod CONNECT TO u_prod IDENTIFIED BY secreto123 USING \'prod\';');
            return false;
        }

        $this->newLine();
        return true;
    }

    private function executePhase1()
    {
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('FASE 1: Configurando COMEE PDB');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();

        $scripts = [
            '01_create_tables_comee.sql' => 'Creando tablas principales en COMEE',
            '02_create_replica_tables_comee.sql' => 'Creando tablas de réplica en COMEE',
            '03_create_synonyms_comee.sql' => 'Creando sinónimos en COMEE',
        ];

        foreach ($scripts as $script => $description) {
            $this->executeScript($this->comeeConnection, $script, $description);
        }
    }

    private function executePhase2()
    {
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('FASE 2: Configurando PROD PDB');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();

        $scripts = [
            '04_create_synonyms_prod.sql' => 'Creando sinónimos en PROD',
            '05_triggers_replication_prod.sql' => 'Creando triggers de replicación en PROD',
        ];

        foreach ($scripts as $script => $description) {
            $this->executeScript($this->prodConnection, $script, $description);
        }
    }

    private function executePhase3()
    {
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('FASE 3: Completando configuración de COMEE');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();

        $scripts = [
            '06_triggers_replication_comee.sql' => 'Creando triggers de replicación en COMEE',
        ];

        foreach ($scripts as $script => $description) {
            $this->executeScript($this->comeeConnection, $script, $description);
        }
    }

    private function executePhase4()
    {
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('FASE 4: Migración de Datos');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->warn('⚠ Esta fase requiere deshabilitar triggers temporalmente');

        if (!$this->option('skip-confirmation')) {
            if (!$this->confirm('¿Continuar con la migración de datos?', true)) {
                $this->warn('Migración de datos omitida. Deberás ejecutarla manualmente.');
                return;
            }
        }

        // Deshabilitar triggers en COMEE
        $this->info('Deshabilitando triggers en COMEE...');
        $this->disableTriggersComee();

        // Ejecutar migración desde PROD
        $this->executeScript($this->prodConnection, '07_migrate_data.sql', 'Migrando datos de PROD a COMEE');

        // Reactivar triggers en COMEE
        $this->info('Reactivando triggers en COMEE...');
        $this->enableTriggersComee();
    }

    private function executePhase5()
    {
        $this->info('════════════════════════════════════════════════════════════════');
        $this->info('FASE 5: Verificación');
        $this->info('════════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->executeScript($this->prodConnection, '99_verification_queries.sql', 'Ejecutando tests de verificación');
    }

    private function executeScript($connection, $scriptName, $description)
    {
        $this->info("📄 {$description}...");

        $scriptPath = database_path("oracle/distributed/{$scriptName}");

        if (!File::exists($scriptPath)) {
            $this->error("  ✗ Archivo no encontrado: {$scriptPath}");
            throw new \Exception("Script no encontrado: {$scriptName}");
        }

        try {
            $sql = File::get($scriptPath);

            // Limpiar el SQL
            $sql = $this->cleanSQL($sql);

            // Separar en statements individuales
            $statements = $this->splitStatements($sql);

            foreach ($statements as $index => $statement) {
                if (empty(trim($statement))) continue;

                try {
                    DB::connection($connection)->statement($statement);
                } catch (\Exception $e) {
                    // Algunos errores son esperados (ej: tabla ya existe)
                    if (strpos($e->getMessage(), 'ORA-00955') !== false) {
                        // Objeto ya existe, continuar
                        continue;
                    }
                    if (strpos($e->getMessage(), 'ORA-00942') !== false) {
                        // Tabla no existe al intentar DROP, continuar
                        continue;
                    }
                    // Otros errores son críticos
                    throw $e;
                }
            }

            $this->info("  ✓ {$description} completado");
            $this->newLine();
        } catch (\Exception $e) {
            $this->error("  ✗ Error ejecutando {$scriptName}: " . $e->getMessage());
            throw $e;
        }
    }

    private function cleanSQL($sql)
    {
        // Remover comentarios de una línea
        $sql = preg_replace('/--.*$/m', '', $sql);

        // Remover PROMPT statements
        $sql = preg_replace('/PROMPT.*$/m', '', $sql);

        // Remover EXIT
        $sql = str_replace('EXIT;', '', $sql);
        $sql = str_replace('exit;', '', $sql);

        // Remover SET commands
        $sql = preg_replace('/SET\s+\w+.*$/m', '', $sql);

        return trim($sql);
    }

    private function splitStatements($sql)
    {
        // Dividir por bloques PL/SQL (BEGIN...END) y statements regulares
        $statements = [];
        $current = '';
        $inBlock = false;
        $lines = explode("\n", $sql);

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (empty($trimmed)) continue;

            // Detectar inicio de bloque PL/SQL
            if (preg_match('/^(BEGIN|DECLARE|CREATE\s+(OR\s+REPLACE\s+)?(TRIGGER|PROCEDURE|FUNCTION))/i', $trimmed)) {
                $inBlock = true;
            }

            $current .= $line . "\n";

            // Detectar fin de bloque
            if ($inBlock && $trimmed === '/') {
                $statements[] = rtrim($current, "\n/");
                $current = '';
                $inBlock = false;
            }
            // Detectar fin de statement regular
            elseif (!$inBlock && substr($trimmed, -1) === ';') {
                $statements[] = rtrim($current);
                $current = '';
            }
        }

        if (!empty(trim($current))) {
            $statements[] = trim($current);
        }

        return $statements;
    }

    private function disableTriggersComee()
    {
        $triggers = [
            'trg_productos_insert_repl',
            'trg_productos_update_repl',
            'trg_productos_delete_repl',
            'trg_facturas_insert_repl',
            'trg_detalle_factura_insert_repl',
        ];

        foreach ($triggers as $trigger) {
            try {
                DB::connection($this->comeeConnection)->statement("ALTER TRIGGER {$trigger} DISABLE");
            } catch (\Exception $e) {
                // Trigger no existe aún, ignorar
            }
        }
    }

    private function enableTriggersComee()
    {
        $triggers = [
            'trg_productos_insert_repl',
            'trg_productos_update_repl',
            'trg_productos_delete_repl',
            'trg_facturas_insert_repl',
            'trg_detalle_factura_insert_repl',
        ];

        foreach ($triggers as $trigger) {
            try {
                DB::connection($this->comeeConnection)->statement("ALTER TRIGGER {$trigger} ENABLE");
            } catch (\Exception $e) {
                $this->warn("No se pudo habilitar {$trigger}: " . $e->getMessage());
            }
        }
    }
}
