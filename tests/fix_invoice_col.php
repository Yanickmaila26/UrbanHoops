<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Adding DFC_TALLA to DETALLE_FACTURA in PROD ---\n";

try {
    DB::connection('oracle')->statement("ALTER TABLE DETALLE_FACTURA ADD (DFC_TALLA VARCHAR2(50))");
    echo "Successfully added DFC_TALLA to DETALLE_FACTURA in PROD.\n";
} catch (\Exception $e) {
    if (str_contains($e->getMessage(), 'ORA-01430')) {
        echo "Column already exists in PROD.\n";
    } else {
        echo "Error in PROD: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Adding DFC_TALLA to DETALLE_FACTURA in COMEE (if exists) ---\n";
try {
    DB::connection('oracle')->statement("ALTER TABLE DETALLE_FACTURA@link_comee ADD (DFC_TALLA VARCHAR2(50))");
    echo "Successfully added DFC_TALLA to DETALLE_FACTURA in COMEE.\n";
} catch (\Exception $e) {
    if (str_contains($e->getMessage(), 'ORA-01430')) {
        echo "Column already exists in COMEE.\n";
    } elseif (str_contains($e->getMessage(), 'ORA-00942')) {
        echo "Table DETALLE_FACTURA does not exist in COMEE (via link).\n";
    } else {
        echo "Error in COMEE: " . $e->getMessage() . "\n";
    }
}
