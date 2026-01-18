<?php

use App\Http\Controllers\BodegaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\OrdenCompraController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('welcome');
Route::get('/productos-servicios', function () {
    return view('productos_servicios');
})->name('productos-servicios');
Route::get('/detalle_producto', function () {
    return view('detalle_producto');
})->name('detalle_producto');
Route::get('/contacto', function () {
    return view('contacto');
})->name('contacto');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    // Inventario
    Route::resource('productos', ProductoController::class)->names('products');
    Route::resource('bodegas', BodegaController::class)->names('warehouse');
    Route::resource('kardex', KardexController::class)->names('kardex');
    Route::get('invoices/cart/{dni}', [FacturaController::class, 'getCart'])->name('invoices.cart'); // AJAX for Cart Integration
    // Ventas
    Route::resource('clientes', ClienteController::class)->names('customers');
    Route::resource('carritos', CarritoController::class)->names('shopping-carts');
    Route::resource('facturas', FacturaController::class)->names('invoices');

    // Compras
    Route::resource('proveedores', ProveedorController::class)->names('suppliers');
    Route::resource('ordenes-compra', OrdenCompraController::class)->names('purchase-orders');
});
