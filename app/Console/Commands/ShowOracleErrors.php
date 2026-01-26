<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowOracleErrors extends Command
{
    protected $signature = 'oracle:errors';
    protected $description = 'Muestra errores de compilación de objetos Oracle';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('  ERRORES DE COMPILACIÓN - ORACLE');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        try {
            $errors = DB::select("
                SELECT 
                    name,
                    type,
                    line,
                    position,
                    text
                FROM user_errors
                WHERE name IN (
                    'SP_FINALIZAR_COMPRA_WEB',
                    'SP_REGISTRAR_INGRESO_BODEGA',
                    'TRG_AUDITORIA_CLIENTES',
                    'TRG_KARDEX_PRODUCTO_BODEGA',
                    'TRG_KARDEX_VENTA'
                )
                ORDER BY name, sequence
            ");

            if (count($errors) === 0) {
                $this->info('✓ No hay errores de compilación');
                return 0;
            }

            $grouped = collect($errors)->groupBy('name');

            foreach ($grouped as $objectName => $objectErrors) {
                $this->error("┌─ {$objectName} ({$objectErrors->first()->type})");

                foreach ($objectErrors as $error) {
                    $this->line("│ Línea {$error->line}:{$error->position} - {$error->text}");
                }

                $this->line('└' . str_repeat('─', 50));
                $this->newLine();
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
