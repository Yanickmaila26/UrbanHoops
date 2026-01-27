<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Checking DETALLE_FACTURA Columns ---\n";

try {
    // Check PROD (default connection)
    echo "\n[PROD] Columns in DETALLE_FACTURA:\n";
    $colsProd = DB::connection('oracle')->select("
        SELECT column_name, data_type, data_length 
        FROM user_tab_columns 
        WHERE table_name = 'DETALLE_FACTURA' 
        ORDER BY column_id
    ");
    foreach ($colsProd as $col) {
        echo " - {$col->column_name} ({$col->data_type})\n";
    }

    // Check COMEE (via link if possible, or secondary connection)
    // Assuming we can use secondary connection 'oracle_comee' if defined, or DB Link 'link_comee' from PROD
    echo "\n[COMEE] Columns in DETALLE_FACTURA (via link_comee):\n";
    try {
        $colsComee = DB::connection('oracle')->select("
            SELECT column_name, data_type, data_length 
            FROM all_tab_columns@link_comee 
            WHERE table_name = 'DETALLE_FACTURA' 
            ORDER BY column_id
        ");
        foreach ($colsComee as $col) {
            echo " - {$col->column_name} ({$col->data_type})\n";
        }
    } catch (\Exception $e) {
        echo "Could not check via link: " . $e->getMessage() . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
