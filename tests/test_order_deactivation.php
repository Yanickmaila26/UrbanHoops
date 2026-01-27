<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kardex;
use Illuminate\Support\Facades\DB;

echo "=== TESTING ORDER DEACTIVATION ===\n\n";

// Create test order
$orden = \App\Models\OrdenCompra::create([
    'ORC_Numero' => 'TEST-DEACT-' . time(),
    'PRV_Ced_Ruc' => \App\Models\Proveedor::first()->PRV_Ced_Ruc ?? '1790085783001',
    'ORC_Fecha_Emision' => now(),
    'ORC_Fecha_Entrega' => now()->addDays(7),
    'ORC_Monto_Total' => 100,
    'ORC_Estado' => true
]);

// Add detail
$prod = \App\Models\Producto::first();
DB::table('DETALLE_ORD_COM')->insert([
    'ORC_NUMERO' => $orden->ORC_Numero,
    'PRO_CODIGO' => $prod->PRO_Codigo,
    'cantidad_solicitada' => 5,
    'DOC_Talla' => 'L',
]);

echo "Order created: {$orden->ORC_Numero}\n";
echo "Order status BEFORE: " . ($orden->ORC_Estado ? 'ACTIVE' : 'INACTIVE') . "\n\n";

// Process kardex
$trn = \App\Models\Transaccion::where('TRN_Tipo', 'E')->first();
$bodega = \App\Models\Bodega::first();

$data = [
    'BOD_Codigo' => $bodega->BOD_Codigo,
    'TRN_Codigo' => $trn->TRN_Codigo,
    'ORC_Numero' => $orden->ORC_Numero,
];

echo "Processing Kardex movement...\n";
Kardex::crearMovimiento($data);
echo "Movement processed.\n\n";

// Refresh order to check status
$orden->refresh();
echo "Order status AFTER: " . ($orden->ORC_Estado ? 'ACTIVE' : 'INACTIVE') . "\n\n";

if (!$orden->ORC_Estado) {
    echo "[PASS] Order was deactivated successfully!\n";
} else {
    echo "[FAIL] Order is still active!\n";
}

// Cleanup
DB::table('kardexes')->where('ORC_NUMERO', $orden->ORC_Numero)->delete();
DB::table('DETALLE_ORD_COM')->where('ORC_NUMERO', $orden->ORC_Numero)->delete();
$orden->delete();
