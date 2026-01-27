<?php
require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Adding CRD_Talla column to DETALLE_CARRITO in COMEE ---\n";

try {
    // Determine connection name for COMEE
    // config/database.php has 'oracle_comee'

    $conn = DB::connection('oracle_comee');

    // Check if column exists first to avoid error
    // But ORA-01430: column being added already exists

    try {
        $conn->statement("ALTER TABLE DETALLE_CARRITO ADD (CRD_Talla VARCHAR2(50))");
        echo "SUCCESS: Column added.\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'ORA-01430') !== false) {
            echo "SKIPPED: Column already exists.\n";
        } else {
            throw $e;
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
