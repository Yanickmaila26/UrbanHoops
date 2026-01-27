<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Carrito;
use App\Models\DetalleCarrito;
use Illuminate\Support\Facades\DB;

echo "--- Debugging Cart Connections ---\n";

try {
    $cart = Carrito::first();
    echo "Carrito Connection: " . ($cart ? $cart->getConnectionName() : 'Default') . "\n";
    echo "Carrito Found: " . ($cart ? $cart->CRC_Carrito : 'None') . "\n";

    echo "Querying DetalleCarrito...\n";
    $dc = DetalleCarrito::first();
    echo "DetalleCarrito Connection: " . ($dc ? $dc->getConnectionName() : 'Default') . "\n";

    if ($dc) {
        echo "Example Detalle: " . $dc->PRO_Codigo . "\n";
    } else {
        echo "No detail items found.\n";
    }

    echo "Current Config DB Connection: " . config('database.default') . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
