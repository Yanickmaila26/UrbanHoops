<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

try {
    echo "--- Test Start ---\n";

    // 1. Get a product
    $prod = Producto::first();
    if (!$prod) throw new Exception("No products found");
    echo "Product: {$prod->PRO_Codigo}\n";

    // 2. Get/Create a cart
    $cartId = 'CRC' . rand(100, 999) . rand(10, 99); // 5-6 chars

    // Get valid client
    $client = Cliente::first();
    if (!$client) {
        // Try to insert a dummy client if none exist? 
        // For now, assume seed data exists or nullable works if client is missing
        // Checking migration, CLI_Ced_Ruc is nullable in Carritos?
        // But FK might enforce it if not null. 
        echo "Warning: No customers found. Trying with NULL client.\n";
        $clientId = null;
    } else {
        $clientId = $client->CLI_Ced_Ruc;
        echo "Using Client: $clientId\n";
    }

    $cart = new Carrito();
    $cart->CRC_Carrito = $cartId;
    $cart->CLI_Ced_Ruc = $clientId;
    $cart->save();

    echo "Cart Created: $cartId\n";

    // 3. Add Item
    $detail = new DetalleCarrito();
    $detail->CRC_Carrito = $cartId;
    $detail->PRO_Codigo = $prod->PRO_Codigo;
    $detail->CRD_Cantidad = 1;
    $detail->CRD_Talla = 'M'; // This column was the issue
    $detail->save();

    echo "Detail Added!\n";
} catch (\Exception $e) {
    echo "ERROR_MSG: " . str_replace(["\r", "\n"], " ", $e->getMessage()) . "\n";
    if ($e instanceof \PDOException) {
        echo "ERROR_CODE: " . $e->getCode() . "\n";
        echo "ERROR_INFO: " . json_encode($e->errorInfo) . "\n";
    }
}
