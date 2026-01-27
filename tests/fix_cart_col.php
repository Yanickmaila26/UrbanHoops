<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Ensuring CRD_Talla in DETALLE_CARRITO in COMEE ---\n";

try {
    DB::connection('oracle')->statement("ALTER TABLE u_comee.DETALLE_CARRITO@link_comee ADD (CRD_TALLA VARCHAR2(50))");
    echo "Added CRD_TALLA to COMEE.\n";
} catch (\Exception $e) {
    if (str_contains($e->getMessage(), 'ORA-01430')) {
        echo "Column CRD_TALLA already exists in COMEE.\n";
    } else {
        echo "Error adding CRD_TALLA: " . $e->getMessage() . "\n";
    }
}
