<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- FIXING PROD DETALLE_FACTURA ---\n";

try {
    DB::connection('oracle')->statement("ALTER TABLE DETALLE_FACTURA ADD (DFC_TALLA VARCHAR2(50))");
    echo "Added DFC_TALLA to PROD physical table.\n";
} catch (\Exception $e) {
    if (str_contains($e->getMessage(), 'ORA-01430')) {
        echo "DFC_TALLA already exists in PROD.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
