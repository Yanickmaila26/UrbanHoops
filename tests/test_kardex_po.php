<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kardex;
use App\Models\OrdenCompra;
use App\Models\Producto;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;

echo "--- TESTING KARDEX PURCHASE ORDER LOGIC ---\n";

// 1. Get a product to test
$prod = Producto::first();
if (!$prod) {
    echo "No products found.\n";
    exit;
}
echo "Testing with Product: {$prod->PRO_Codigo} ({$prod->PRO_Nombre})\n";
echo "Initial Stock: {$prod->PRO_Stock}\n";

// 2. Create a dummy Order
$orderId = 'OC-TEST-' . time();
echo "Creating dummy Order: $orderId\n";

$orden = OrdenCompra::create([
    'ORC_Numero' => $orderId,
    'PRV_Ced_Ruc' => \App\Models\Proveedor::first()->PRV_Ced_Ruc ?? '1790085783001',
    'ORC_Fecha_Emision' => now(),
    'ORC_Fecha_Entrega' => now(),
    'ORC_Monto_Total' => 100,
    'ORC_Estado' => true
]);

// Attach product with quantity 5 and size 'M'
$qty = 5;
$size = 'M';
DB::table('DETALLE_ORD_COM')->insert([
    'ORC_NUMERO' => $orderId,
    'PRO_CODIGO' => $prod->PRO_Codigo,
    'cantidad_solicitada' => $qty, // matches migration
    'DOC_Talla' => $size,
]);

// 3. Process Kardex Entry (T01 - Compra/Ingreso)
echo "Processing Kardex Entry (Ingreso)...\n";
$bodega = \App\Models\Bodega::first();
if (!$bodega) die("No bodegas found.");
$trn = \App\Models\Transaccion::where('TRN_Tipo', 'E')->first();
if (!$trn) die("No transaction type E found.");

$data = [
    'BOD_Codigo' => $bodega->BOD_Codigo,
    'TRN_Codigo' => $trn->TRN_Codigo,
    'ORC_Numero' => $orderId,
    'PRO_Codigo' => null,
    'KAR_CANTIDAD' => null
];

try {
    Kardex::crearMovimiento($data);
    echo "Movimiento created.\n";
} catch (\Exception $e) {
    echo "Error processing kardex: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";

    echo "\nExisting IDs:\n";
    $ids = DB::table('kardexes')->limit(5)->pluck('KAR_CODIGO');
    print_r($ids);
    exit;
}

// 4. Verify Stock Update
$prod->refresh();
echo "New Stock: {$prod->PRO_Stock}\n";

if ($prod->PRO_Stock > 0) { // Assuming it increased
    echo "[PASS] Stock updated.\n";
} else {
    echo "[FAIL] Stock did not increase.\n";
}

// Check JSON Talla
echo "Talla JSON: " . json_encode($prod->PRO_Talla) . "\n";

// Cleanup
$orden->delete();
DB::table('DETALLE_ORD_COM')->where('ORC_NUMERO', $orderId)->delete();
