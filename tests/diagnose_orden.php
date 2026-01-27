<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OrdenCompra;
use Illuminate\Support\Facades\DB;

echo "=== DIAGNOSING ORDEN COMPRA -> PRODUCTOS RELATION ===\n\n";

// Get first active order
$orden = OrdenCompra::where('ORC_Estado', true)->first();

if (!$orden) {
    echo "No active orders found.\n";
    exit;
}

echo "Order: {$orden->ORC_Numero}\n";
echo "Provider: {$orden->PRV_Ced_Ruc}\n\n";

// Test Eloquent relation
echo "--- Testing Eloquent Relation ---\n";
$productos = $orden->productos;
echo "Products count (Eloquent): " . $productos->count() . "\n";

if ($productos->count() > 0) {
    foreach ($productos as $prod) {
        echo "  - Product: {$prod->PRO_Codigo}\n";
        echo "    Pivot cantidad_solicitada: " . ($prod->pivot->cantidad_solicitada ?? 'NULL') . "\n";
        echo "    Pivot CANTIDAD_SOLICITADA: " . ($prod->pivot->CANTIDAD_SOLICITADA ?? 'NULL') . "\n";
        echo "    Pivot DOC_Talla: " . ($prod->pivot->DOC_Talla ?? 'NULL') . "\n";
        echo "    Pivot DOC_TALLA: " . ($prod->pivot->DOC_TALLA ?? 'NULL') . "\n";
    }
} else {
    echo "  [WARNING] No products loaded via Eloquent!\n";
}

// Test raw query
echo "\n--- Testing Raw Query ---\n";
$detalles = DB::table('DETALLE_ORD_COM')
    ->where('ORC_NUMERO', $orden->ORC_Numero)
    ->get();

echo "Details count (Raw): " . $detalles->count() . "\n";

if ($detalles->count() > 0) {
    foreach ($detalles as $detalle) {
        echo "  - Product: " . ($detalle->PRO_CODIGO ?? $detalle->pro_codigo) . "\n";
        echo "    Cantidad: " . ($detalle->CANTIDAD_SOLICITADA ?? $detalle->cantidad_solicitada) . "\n";
        echo "    Talla: " . ($detalle->DOC_TALLA ?? $detalle->DOC_Talla ?? 'NULL') . "\n";
    }
}

// Check table structure
echo "\n--- Table Structure ---\n";
$cols = DB::select("SELECT column_name FROM user_tab_columns WHERE table_name = 'DETALLE_ORD_COM'");
echo "Columns: " . implode(', ', array_column($cols, 'column_name')) . "\n";
