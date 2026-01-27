<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Carrito;
use App\Models\Producto;

echo "--- CHECKING CART CALCULATION ---\n";

$carrito = Carrito::with('detalles.producto')->first();

if (!$carrito) {
    echo "No carts found. Creating a test cart...\n";
    // Creating test logic similar to controller
    // ... skipped for brevity, user likely has carts if they are testing
    echo "Skipping test as no cart exists.\n";
    exit;
}

echo "Cart ID: " . $carrito->CRC_Carrito . "\n";
echo "Items:\n";
foreach ($carrito->detalles as $detalle) {
    $price = $detalle->producto->PRO_Precio;
    $qty = $detalle->CRD_Cantidad;
    $sub = $price * $qty;
    echo " - " . $detalle->producto->PRO_Nombre . ": $qty x $price = $sub\n";
}

$subtotal = $carrito->getSubtotal();
$iva = $carrito->getIva();
$total = $carrito->getTotal();
$rate = config('urbanhoops.iva');

echo "\nSummary:\n";
echo "Config Rate: $rate%\n";
echo "Subtotal: " . number_format($subtotal, 2) . "\n";
echo "IVA:      " . number_format($iva, 2) . "\n";
echo "Total:    " . number_format($total, 2) . "\n";

if ($iva == $subtotal * ($rate / 100)) {
    echo "\n[PASS] Calculation looks correct.\n";
} else {
    echo "\n[FAIL] Calculation mismatch.\n";
}
