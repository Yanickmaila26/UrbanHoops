<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kardex;
use Illuminate\Support\Facades\DB;

echo "=== TESTING KARDEX WITH REAL ORDER ===\n\n";

// Get first active order
$orden = \App\Models\OrdenCompra::where('ORC_Estado', true)->first();
if (!$orden) {
    echo "No active orders found. Creating test order...\n";

    // Create test order
    $orden = \App\Models\OrdenCompra::create([
        'ORC_Numero' => 'TEST-' . time(),
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
        'cantidad_solicitada' => 10,
        'DOC_Talla' => 'M',
    ]);

    echo "Test order created: {$orden->ORC_Numero}\n\n";
}

echo "Order: {$orden->ORC_Numero}\n";

// Count kardex before
$kardexBefore = DB::table('kardexes')->where('ORC_NUMERO', $orden->ORC_Numero)->count();
echo "Kardex records before: $kardexBefore\n\n";

// Get transaction type E (Entry)
$trn = \App\Models\Transaccion::where('TRN_Tipo', 'E')->first();
$bodega = \App\Models\Bodega::first();

if (!$trn || !$bodega) {
    echo "Missing transaction type or bodega!\n";
    exit;
}

$data = [
    'BOD_Codigo' => $bodega->BOD_Codigo,
    'TRN_Codigo' => $trn->TRN_Codigo,
    'ORC_Numero' => $orden->ORC_Numero,
];

echo "Processing Kardex movement...\n";
try {
    Kardex::crearMovimiento($data);
    echo "SUCCESS: Movement created\n\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n\n";
}

// Count kardex after
$kardexAfter = DB::table('kardexes')->where('ORC_NUMERO', $orden->ORC_Numero)->count();
echo "Kardex records after: $kardexAfter\n";
echo "New records created: " . ($kardexAfter - $kardexBefore) . "\n\n";

if ($kardexAfter > $kardexBefore) {
    echo "[PASS] Kardex records were created!\n";

    // Show the records
    $records = DB::table('kardexes')->where('ORC_NUMERO', $orden->ORC_Numero)->get();
    foreach ($records as $record) {
        echo "  - KAR_CODIGO: " . ($record->KAR_CODIGO ?? $record->kar_codigo) . "\n";
        echo "    PRO_CODIGO: " . ($record->PRO_CODIGO ?? $record->pro_codigo) . "\n";
        echo "    KAR_CANTIDAD: " . ($record->KAR_CANTIDAD ?? $record->kar_cantidad) . "\n";
    }
} else {
    echo "[FAIL] No kardex records were created!\n";
}
