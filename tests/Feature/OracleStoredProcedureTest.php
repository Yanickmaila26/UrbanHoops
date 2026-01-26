<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\OracleStoredProcedureService;
use App\Models\Cliente;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\Producto;

class OracleStoredProcedureTest extends TestCase
{
    /**
     * Test del procedimiento almacenado sp_finalizar_compra_web
     *
     * @return void
     */
    public function test_finalizar_compra_web_procedure()
    {
        // Skip if not using Oracle
        if (config('database.default') !== 'oracle') {
            $this->markTestSkipped('Test solo para Oracle');
        }

        $service = new OracleStoredProcedureService();

        // Crear datos de prueba
        $cliente = Cliente::factory()->create();
        $producto = Producto::factory()->create(['PRO_Precio' => 100]);

        $carrito = Carrito::create([
            'CRC_Carrito' => 'CRCTEST01',
            'CLI_Ced_Ruc' => $cliente->CLI_Ced_Ruc
        ]);

        DetalleCarrito::create([
            'CRC_Carrito' => $carrito->CRC_Carrito,
            'PRO_Codigo' => $producto->PRO_Codigo,
            'CRD_Cantidad' => 2,
            'CRD_Talla' => 'L'
        ]);

        // Ejecutar procedimiento
        $result = $service->finalizarCompraWeb($carrito->CRC_Carrito);

        // Verificaciones
        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['factura_id']);
        $this->assertStringContainsString('exitosamente', $result['message']);

        // Verificar que se creó la factura
        $this->assertDatabaseHas('facturas', [
            'FAC_CODIGO' => $result['factura_id'],
            'CLI_CED_RUC' => $cliente->CLI_Ced_Ruc
        ]);

        // Verificar que el carrito fue limpiado
        $this->assertDatabaseMissing('carritos', [
            'CRC_CARRITO' => $carrito->CRC_Carrito
        ]);
    }

    /**
     * Test del trigger de auditoría
     *
     * @return void
     */
    public function test_auditoria_trigger()
    {
        // Skip if not using Oracle
        if (config('database.default') !== 'oracle') {
            $this->markTestSkipped('Test solo para Oracle');
        }

        $service = new OracleStoredProcedureService();

        // Crear cliente
        $cliente = Cliente::factory()->create([
            'CLI_CORREO' => 'original@test.com',
            'CLI_DIRECCION' => 'Dirección Original'
        ]);

        // Actualizar correo
        $cliente->update(['CLI_CORREO' => 'nuevo@test.com']);

        // Verificar que se registró en el log
        $auditoria = $service->obtenerAuditoriaCliente($cliente->CLI_Ced_Ruc);

        $this->assertNotEmpty($auditoria);
        $this->assertEquals('CLI_CORREO', $auditoria[0]->LOG_CAMPO);
        $this->assertEquals('original@test.com', $auditoria[0]->LOG_VALOR_ANTERIOR);
        $this->assertEquals('nuevo@test.com', $auditoria[0]->LOG_VALOR_NUEVO);
    }
}
