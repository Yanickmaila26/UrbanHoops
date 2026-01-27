<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING OrdenCompra->getProductosWithDetails() ===\n\n";

// Get first order
$orden = \App\Models\OrdenCompra::first();

if (!$orden) {
    echo "No orders found in database.\n";
    exit;
}

echo "Order: {$orden->ORC_Numero}\n";
echo "Provider: {$orden->PRV_Ced_Ruc}\n\n";

// Test using Eloquent relation (broken)
echo "--- Eloquent productos (broken) ---\n";
$productosEloquent = $orden->productos;
echo "Count: " . $productosEloquent->count() . "\n";

if ($productosEloquent->count() > 0) {
    foreach ($productosEloquent as $prod) {
        echo "  - {$prod->PRO_Codigo}: Qty = " . ($prod->pivot->cantidad_solicitada ?? 'NULL') . "\n";
    }
} else {
    echo "  [FAIL] No products loaded via Eloquent\n";
}

// Test using helper method (fixed)
echo "\n--- Helper method getProductosWithDetails() (fixed) ---\n";
$productosFixed = $orden->getProductosWithDetails();
echo "Count: " . $productosFixed->count() . "\n";

if ($productosFixed->count() > 0) {
    echo "[PASS] Products loaded successfully!\n";
    foreach ($productosFixed as $prod) {
        echo "  - {$prod->PRO_Codigo}: {$prod->PRO_Nombre}\n";
        echo "    Qty: " . ($prod->pivot->cantidad_solicitada ?? 'NULL') . "\n";
        echo "    Talla: " . ($prod->pivot->DOC_Talla ?? 'NULL') . "\n";
    }
} else {
    echo "[FAIL] No products loaded\n";
}
