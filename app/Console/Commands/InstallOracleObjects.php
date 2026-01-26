<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallOracleObjects extends Command
{
    protected $signature = 'oracle:install';
    protected $description = 'Instala todos los procedimientos almacenados y triggers de Oracle';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('  INSTALACIÓN DE OBJETOS ORACLE');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        $oracleDir = database_path('oracle');

        $files = [
            'sp_finalizar_compra_web.sql' => 'Procedimiento: Finalizar Compra Web',
            'trg_auditoria_clientes.sql' => 'Trigger: Auditoría de Clientes',
            'sp_registrar_ingreso_bodega.sql' => 'Procedimiento: Ingreso a Bodega',
            'trg_movimiento_kardex.sql' => 'Triggers: Movimiento de Kardex',
            // 'sp_emitir_factura.sql' requiere instalación manual (demasiado complejo)
            'trg_bloqueo_facturas.sql' => 'Trigger: Bloqueo de Facturas',
        ];

        $installed = 0;
        $failed = 0;

        foreach ($files as $file => $description) {
            $filePath = $oracleDir . DIRECTORY_SEPARATOR . $file;

            if (!File::exists($filePath)) {
                $this->warn("⚠ Archivo no encontrado: {$file}");
                continue;
            }

            $this->line("Instalando: {$description}...");

            try {
                $sql = File::get($filePath);

                // Limpiar el SQL (remover comentarios de una línea y el terminador /)
                $sql = preg_replace('/--.*$/m', '', $sql);
                $sql = str_replace('/', '', $sql);
                $sql = trim($sql);

                // Si el archivo contiene múltiples statements (triggers), separarlos
                if (strpos($file, 'trg_movimiento_kardex') !== false) {
                    // Este archivo tiene 2 triggers separados
                    $statements = preg_split('/CREATE OR REPLACE TRIGGER/i', $sql);

                    foreach ($statements as $index => $stmt) {
                        if (empty(trim($stmt))) continue;

                        $stmt = 'CREATE OR REPLACE TRIGGER' . $stmt;
                        $this->executeStatement($stmt, "  Trigger " . ($index));
                    }
                } else {
                    $this->executeStatement($sql, "  {$description}");
                }

                $this->info("  ✓ {$description} instalado");
                $installed++;
            } catch (\Exception $e) {
                $this->error("  ✗ Error: " . $e->getMessage());
                $failed++;
            }

            $this->newLine();
        }

        $this->info('═══════════════════════════════════════════');
        $this->info("  Instalados: {$installed} | Fallidos: {$failed}");
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        if ($failed === 0) {
            $this->info('✓ Todos los objetos instalados correctamente');
            $this->newLine();
            $this->info('Ejecuta: php artisan oracle:test');
            $this->info('Para verificar que todo funciona');
        } else {
            $this->warn('⚠ Algunos objetos fallaron - revisa los errores arriba');
        }

        return $failed === 0 ? 0 : 1;
    }

    private function executeStatement(string $sql, string $name)
    {
        // Ejecutar usando DB::statement para PL/SQL
        DB::statement($sql);
    }
}
