<?php

use App\Http\Controllers\BodegaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubcategoriaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
})->name('welcome');
Route::get('/productos-servicios', [App\Http\Controllers\PublicController::class, 'catalogo'])->name('productos-servicios');
Route::get('/producto/{producto}', [App\Http\Controllers\PublicController::class, 'show'])->name('public.products.show');
Route::get('/contacto', function () {
    return view('contacto');
})->name('contacto');
Route::post('/contacto', [App\Http\Controllers\PublicController::class, 'submitContact'])->name('contacto.submit');



// Client Authentication
Route::get('/login', [App\Http\Controllers\ClientAuthController::class, 'showLoginForm'])->name('client.login');
Route::post('/login', [App\Http\Controllers\ClientAuthController::class, 'login'])->name('client.login.submit');
Route::get('/register', [App\Http\Controllers\ClientAuthController::class, 'showRegisterForm'])->name('client.register');
Route::post('/register', [App\Http\Controllers\ClientAuthController::class, 'register'])->name('client.register.submit');
Route::post('/logout', [App\Http\Controllers\ClientAuthController::class, 'logout'])->name('client.logout');

// Client Area (Protected)
Route::middleware(['auth:client', 'role:ROLE_APP_FRONTEND,client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\ClientAreaController::class, 'index'])->name('dashboard');
    Route::get('/cart', [App\Http\Controllers\ClientAreaController::class, 'cart'])->name('cart');
    Route::get('/orders', [App\Http\Controllers\ClientAreaController::class, 'orders'])->name('orders');
    Route::get('/invoices', [App\Http\Controllers\ClientAreaController::class, 'invoices'])->name('invoices');
    Route::get('/addresses', [App\Http\Controllers\ClientAreaController::class, 'addresses'])->name('addresses');
    Route::post('/billing', [App\Http\Controllers\ClientAreaController::class, 'storeBillingProfile'])->name('billing.store');
    Route::delete('/billing/{id}', [App\Http\Controllers\ClientAreaController::class, 'destroyBillingProfile'])->name('billing.destroy');

    // Checkout Routes
    Route::get('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'processPayment'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [App\Http\Controllers\Client\CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/invoice/{order}', [App\Http\Controllers\Client\CheckoutController::class, 'downloadInvoice'])->name('checkout.invoice');
});


// Cart API Routes (Allow Guest Access)
Route::prefix('api/cart')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\CartApiController::class, 'index']);
    Route::post('/sync', [App\Http\Controllers\Api\CartApiController::class, 'sync']);
    Route::post('/add', [App\Http\Controllers\Api\CartApiController::class, 'add']);
    Route::delete('/{itemId}', [App\Http\Controllers\Api\CartApiController::class, 'remove']);
    Route::put('/{itemId}', [App\Http\Controllers\Api\CartApiController::class, 'update']);
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin Routes with Role Protections
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {

    // Shared Resources (Multiple roles can access)
    // Ordenes de Compra: ROLE_ADMIN_PROD (Total), ROLE_GESTOR_INV (Operativo)
    Route::group(['middleware' => ['role:ROLE_ADMIN_PROD|ROLE_GESTOR_INV']], function () {
        Route::resource('ordenes-compra', OrdenCompraController::class)->names('purchase-orders');
    });

    // Facturas: ROLE_ADMIN_PROD (Access Total - Ventas)
    // Gestor Inv usually doesn't manage billing unless specified? 
    // User Grant: ROLE_ADMIN_PROD has SELECT on FACTURA. u_prod (system) has INSERT.
    // We allow ROLE_ADMIN_PROD to access basic invoice management.
    Route::group(['middleware' => ['role:ROLE_ADMIN_PROD']], function () {
        Route::resource('facturas', FacturaController::class)->names('invoices');
        Route::get('invoices/cart/{dni}', [FacturaController::class, 'getCart'])->name('invoices.cart');
    });

    // Clientes & Carritos: ROLE_ADMIN_PROD
    Route::group(['middleware' => ['role:ROLE_ADMIN_PROD']], function () {
        Route::resource('clientes', ClienteController::class)->names('customers');
        Route::resource('carritos', CarritoController::class)->names('shopping-carts');
    });

    // Bodega & Kardex: ROLE_ADMIN_PROD, ROLE_GESTOR_INV
    Route::group(['middleware' => ['role:ROLE_ADMIN_PROD|ROLE_GESTOR_INV']], function () {
        Route::resource('bodegas', BodegaController::class)->names('warehouse');
        Route::resource('kardex', KardexController::class)->names('kardex');
    });

    // Productos & Proveedores: ROLE_ADMIN_PROD, ROLE_GESTOR_INV
    Route::group(['middleware' => ['role:ROLE_ADMIN_PROD|ROLE_GESTOR_INV']], function () {
        Route::resource('productos', ProductoController::class)->names('products');
        Route::resource('proveedores', ProveedorController::class)->names('suppliers');
        Route::resource('categorias', CategoriaController::class)->names('categories');
        Route::resource('subcategorias', SubcategoriaController::class)->names('subcategories');
    });
});
