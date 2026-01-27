<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MigrateFreshDistributed extends Command
{
    protected $signature = 'migrate:fresh-distributed {--seed : Seed the database after migration}';
    protected $description = 'Drop all tables and re-run migrations for distributed Oracle setup';

    private $prodConn = 'oracle';
    private $comeeConn = 'oracle_comee';

    // Tablas que van SOLO en COMEE
    private $tablasComee = [
        'clientes',
        'datos_facturacion',
        'carritos',
        'detalle_carrito',
        'pedidos',
    ];

    // Tablas que van en PROD (y se replican a COMEE)
    private $tablasReplica = [
        'productos',
        'facturas',
        'detalle_factura',
    ];

    public function handle()
    {
        if (!$this->confirm('⚠️  This will DROP ALL TABLES in both PROD and COMEE. Continue?', false)) {
            $this->info('Operation cancelled.');
            return 0;
        }

        try {
            // Paso 1: Limpiar PROD
            $this->info('🗑️  Limpiando PROD...');
            $this->dropAllTables($this->prodConn);
            $this->info('✅ PROD limpiado');
            $this->newLine();

            // Paso 2: Limpiar COMEE
            $this->info('🗑️  Limpiando COMEE...');
            $this->dropAllTables($this->comeeConn);
            $this->info('✅ COMEE limpiado');
            $this->newLine();

            // Paso 3: Crear tablas en PROD
            $this->info('📦 Creando tablas en PROD...');
            $this->createTablesInProd();
            $this->info('✅ Tablas creadas en PROD');
            $this->newLine();

            // Paso 4: Crear tablas en COMEE
            $this->info('📦 Creando tablas en COMEE...');
            $this->createTablesInComee();
            $this->info('✅ Tablas creadas en COMEE');
            $this->newLine();

            // Paso 5: Crear sinónimos
            $this->info('🔗 Creando sinónimos...');
            $this->createSynonyms();
            $this->info('✅ Sinónimos creados');
            $this->newLine();

            // Paso 6: Crear triggers de replicación
            $this->info('⚡ Creando triggers de replicación...');
            $this->createReplicationTriggers();
            $this->info('✅ Triggers creados');
            $this->newLine();

            // Paso 7: Seeders
            if ($this->option('seed')) {
                $this->info('🌱 Ejecutando seeders...');
                Artisan::call('db:seed', [], $this->getOutput());
                $this->info('✅ Seeders completados');
                $this->newLine();
            }

            $this->info('🎉 Migración distribuida completada!');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function dropAllTables($connection)
    {
        DB::connection($connection)->statement("
            BEGIN
                FOR c IN (SELECT table_name FROM user_tables WHERE secondary = 'N') LOOP
                    EXECUTE IMMEDIATE ('DROP TABLE \"' || c.table_name || '\" CASCADE CONSTRAINTS');
                END LOOP;
                
                FOR s IN (
                    SELECT sequence_name 
                    FROM user_sequences 
                    WHERE sequence_name NOT IN (
                        SELECT COALESCE(sequence_name, 'NULL') 
                        FROM user_tab_identity_cols
                        WHERE sequence_name IS NOT NULL
                    )
                ) LOOP
                    EXECUTE IMMEDIATE ('DROP SEQUENCE ' || s.sequence_name);
                END LOOP;
                
                FOR syn IN (SELECT synonym_name FROM user_synonyms) LOOP
                    EXECUTE IMMEDIATE ('DROP SYNONYM ' || syn.synonym_name);
                END LOOP;
            END;
        ");
    }

    private function createTablesInProd()
    {
        // Ejecutar migraciones normales en PROD, pero skip las tablas de COMEE
        $migrations = $this->getMigrationFiles();

        foreach ($migrations as $migration) {
            $tableName = $this->extractTableName($migration);

            // Skip tablas que van solo en COMEE
            if (in_array($tableName, $this->tablasComee)) {
                continue;
            }

            $this->line("  Migrando: $migration");
            require_once database_path("migrations/$migration");

            $class = $this->getMigrationClass($migration);
            $instance = new $class;
            $instance->up();
        }
    }

    private function createTablesInComee()
    {
        // Ejecutar solo las migraciones de tablas que van en COMEE
        $migrations = $this->getMigrationFiles();

        // Cambiar conexión temporal a COMEE
        config(['database.default' => 'oracle_comee']);

        foreach ($migrations as $migration) {
            $tableName = $this->extractTableName($migration);

            // Solo crear tablas que van en COMEE o réplicas
            if (!in_array($tableName, array_merge($this->tablasComee, $this->tablasReplica))) {
                continue;
            }

            $this->line("  Migrando: $migration");
            require_once database_path("migrations/$migration");

            $class = $this->getMigrationClass($migration);
            $instance = new $class;
            $instance->up();
        }

        // Restaurar conexión
        config(['database.default' => 'oracle']);
    }

    private function createSynonyms()
    {
        // Sinónimos en PROD apuntando a COMEE
        foreach ($this->tablasComee as $tabla) {
            $sql = "CREATE SYNONYM " . strtoupper($tabla) . " FOR u_comee." . strtoupper($tabla) . "@link_comee";
            try {
                DB::connection($this->prodConn)->statement($sql);
                $this->line("  ✓ " . strtoupper($tabla));
            } catch (\Exception $e) {
                $this->warn("  ⚠ Error en $tabla: " . $e->getMessage());
            }
        }
    }

    private function createReplicationTriggers()
    {
        $baseDir = database_path('oracle/distributed');

        // Triggers en PROD
        $this->executeScriptFile($this->prodConn, "$baseDir/05_triggers_replication_prod.sql");

        // Triggers en COMEE
        $this->executeScriptFile($this->comeeConn, "$baseDir/06_triggers_replication_comee.sql");
    }

    private function executeScriptFile($connection, $scriptPath)
    {
        if (!File::exists($scriptPath)) {
            $this->warn("  Archivo no encontrado: $scriptPath");
            return;
        }

        $sql = File::get($scriptPath);

        // Limpiar y dividir
        $sql = preg_replace('/^\s*PROMPT.*$/m', '', $sql);
        $sql = preg_replace('/^\s*EXIT.*;?$/mi', '', $sql);
        $sql = preg_replace('/^\s*SET\s+\w+.*$/m', '', $sql);

        $rawBlocks = preg_split('/^\s*\/\s*$/m', $sql);

        foreach ($rawBlocks as $block) {
            $block = trim($block);
            if (empty($block) || strlen($block) < 10) continue;

            try {
                DB::connection($connection)->statement($block);
            } catch (\Exception $e) {
                // Ignorar errores de objeto ya existe
                if (strpos($e->getMessage(), 'ORA-00955') === false) {
                    $this->warn("  ⚠ " . substr($e->getMessage(), 0, 80));
                }
            }
        }
    }

    private function getMigrationFiles()
    {
        $files = File::files(database_path('migrations'));
        $migrations = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $migrations[] = $file->getFilename();
            }
        }

        sort($migrations);
        return $migrations;
    }

    private function extractTableName($filename)
    {
        // Extract table name from migration filename
        // e.g., "2026_01_06_010319_create_clientes_table.php" => "clientes"
        if (preg_match('/_create_(.+?)_table\.php$/', $filename, $matches)) {
            return $matches[1];
        }
        return '';
    }

    private function getMigrationClass($filename)
    {
        $name = str_replace('.php', '', $filename);
        $parts = explode('_', $name, 5);
        $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $parts[4] ?? $parts[3])));

        return $class;
    }
}
