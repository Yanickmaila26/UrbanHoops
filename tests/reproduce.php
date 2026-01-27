<?php

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
    // We'll use a dummy ID for testing
    $cartId = 'TEST_' . time();

    // Try to create manually to see if it hits PROD table 
    // note: using raw insert to bypass model logic first? No, let's use Model.
    $cart = new Carrito();
    $cart->CRC_Carrito = $cartId;
    $cart->CLI_Ced_Ruc = '9999999999001'; // Default test client
    $cart->save();

    echo "Cart Created: $cartId\n";

    // 3. Add Item
    $detail = new DetalleCarrito();
    $detail->CRC_Carrito = $cartId;
    $detail->PRO_Codigo = $prod->PRO_Codigo;
    $detail->CRD_Cantidad = 1;
    $detail->CRD_Talla = 'M';
    $detail->save();

    echo "Detail Added!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    if ($e instanceof \PDOException) {
        echo "SQL: " . $e->errorInfo[2] . "\n";
    }
}
