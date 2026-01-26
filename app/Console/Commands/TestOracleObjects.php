<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\OracleStoredProcedureService;
use Exception;

class TestOracleObjects extends Command
{
    protected $signature = 'oracle:test';
    protected $description = 'Prueba todos los procedimientos almacenados y triggers de Oracle';

    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new OracleStoredProcedureService();
    }

    public function handle()
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('  PRUEBAS DE OBJETOS ORACLE - UrbanHoops');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        // Test 1: Verificar que los objetos existen
        $this->testObjectsExist();

        // Test 2: Verificar tabla LOG_CLIENTES
        $this->testLogClientesTable();

        // Test 3: Probar trigger de auditoría
        $this->testAuditoriaTrigger();

        // Test 4: Verificar tabla KARDEXES
        $this->testKardexTable();

        // Test 5: Probar procedimiento finalizar compra (si hay datos)
        $this->testFinalizarCompra();

        $this->newLine();
        $this->info('═══════════════════════════════════════════');
        $this->info('  PRUEBAS COMPLETADAS');
        $this->info('═══════════════════════════════════════════');

        return 0;
    }

    private function testObjectsExist()
    {
        $this->info('[TEST 1] Verificando existencia de objetos Oracle...');

        try {
            $objects = DB::select("
                SELECT object_name, object_type, status 
                FROM user_objects 
                WHERE object_name IN (
                    'SP_FINALIZAR_COMPRA_WEB',
                    'SP_REGISTRAR_INGRESO_BODEGA',
                    'TRG_AUDITORIA_CLIENTES',
                    'TRG_KARDEX_PRODUCTO_BODEGA',
                    'TRG_KARDEX_VENTA',
                    'LOG_CLIENTES'
                )
                ORDER BY object_type, object_name
            ");

            if (count($objects) === 0) {
                $this->error('✗ No se encontraron objetos Oracle');
                $this->warn('  Debes instalar los objetos primero:');
                $this->warn('  sqlplus usuario/password@servicio');
                $this->warn('  @database/oracle/install_all.sql');
                return;
            }

            $this->table(
                ['Objeto', 'Tipo', 'Estado'],
                array_map(fn($obj) => [
                    $obj->object_name,
                    $obj->object_type,
                    $obj->status
                ], $objects)
            );

            $allValid = collect($objects)->every(fn($obj) => $obj->status === 'VALID');

            if ($allValid) {
                $this->info('✓ Todos los objetos están VALID');
            } else {
                $this->warn('⚠ Algunos objetos están INVALID - revisa los errores en Oracle');
            }
        } catch (Exception $e) {
            $this->error('✗ Error al verificar objetos: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function testLogClientesTable()
    {
        $this->info('[TEST 2] Verificando tabla LOG_CLIENTES...');

        try {
            $count = DB::table('log_clientes')->count();
            $this->info("✓ Tabla LOG_CLIENTES existe ({$count} registros)");
        } catch (Exception $e) {
            $this->error('✗ Tabla LOG_CLIENTES no existe o no es accesible');
            $this->warn('  Ejecuta: @database/oracle/install_all.sql en Oracle');
        }

        $this->newLine();
    }

    private function testAuditoriaTrigger()
    {
        $this->info('[TEST 3] Probando trigger de auditoría...');

        try {
            // Buscar un cliente
            $cliente = DB::table('clientes')->first();

            if (!$cliente) {
                $this->warn('⚠ No hay clientes en la BD - saltando test');
                $this->newLine();
                return;
            }

            $logsAntes = DB::table('log_clientes')->count();

            // Actualizar correo
            $nuevoCorreo = 'test_' . time() . '@example.com';
            DB::table('clientes')
                ->where('cli_ced_ruc', $cliente->cli_ced_ruc)
                ->update(['cli_correo' => $nuevoCorreo]);

            $logsDespues = DB::table('log_clientes')->count();

            if ($logsDespues > $logsAntes) {
                $this->info('✓ Trigger de auditoría funciona correctamente');

                $ultimoLog = DB::table('log_clientes')
                    ->orderByDesc('log_fecha')
                    ->first();

                $this->line("  Campo: {$ultimoLog->log_campo}");
                $this->line("  Nuevo valor: {$ultimoLog->log_valor_nuevo}");
            } else {
                $this->warn('⚠ Trigger no se ejecutó - verifica que esté instalado');
            }
        } catch (Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function testKardexTable()
    {
        $this->info('[TEST 4] Verificando tabla KARDEXES...');

        try {
            $count = DB::table('kardexes')->count();
            $this->info("✓ Tabla KARDEXES existe ({$count} registros)");

            if ($count > 0) {
                $ultimo = DB::table('kardexes')
                    ->orderByDesc('kar_fecha')
                    ->first();

                $this->line("  Último movimiento: {$ultimo->kar_tipo_movimiento}");
                $this->line("  Saldo actual: {$ultimo->kar_saldo_actual}");
            }
        } catch (Exception $e) {
            $this->warn('⚠ Tabla KARDEXES no tiene datos o no existe');
        }

        $this->newLine();
    }

    private function testFinalizarCompra()
    {
        $this->info('[TEST 5] Probando procedimiento sp_finalizar_compra_web...');

        try {
            // Verificar si existe el procedimiento
            $exists = DB::select("
                SELECT COUNT(*) as count
                FROM user_objects 
                WHERE object_name = 'SP_FINALIZAR_COMPRA_WEB' 
                AND object_type = 'PROCEDURE'
                AND status = 'VALID'
            ");

            if ($exists[0]->count == 0) {
                $this->warn('⚠ Procedimiento no instalado - ejecuta install_all.sql en Oracle');
                $this->newLine();
                return;
            }

            // Crear carrito de prueba
            $cliente = DB::table('clientes')->first();
            $producto = DB::table('productos')->first();

            if (!$cliente || !$producto) {
                $this->warn('⚠ No hay datos suficientes - necesitas clientes y productos');
                $this->newLine();
                return;
            }

            $carritoId = 'CRC' . substr((string)time(), -8); // CRC + 8 dígitos = 11 chars

            // Crear carrito
            DB::table('carritos')->insert([
                'crc_carrito' => $carritoId,
                'cli_ced_ruc' => $cliente->cli_ced_ruc,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Agregar producto
            DB::table('detalle_carrito')->insert([
                'crc_carrito' => $carritoId,
                'pro_codigo' => $producto->pro_codigo,
                'crd_cantidad' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->line("  Carrito creado: {$carritoId}");

            // Llamar al procedimiento
            $result = $this->service->finalizarCompraWeb($carritoId);

            if ($result['success']) {
                $this->info('✓ Procedimiento ejecutado exitosamente');
                $this->line("  {$result['message']}");
                $this->line("  Factura: {$result['factura_id']}");
            } else {
                $this->error('✗ Procedimiento falló: ' . $result['message']);
            }
        } catch (Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
        }

        $this->newLine();
    }
}
