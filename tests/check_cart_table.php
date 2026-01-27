<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Checking DETALLE_CARRITO in COMEE ---\n";

try {
    $cols = DB::connection('oracle')->select("
        SELECT column_name, data_type 
        FROM all_tab_columns@link_comee 
        WHERE table_name = 'DETALLE_CARRITO'
    ");

    foreach ($cols as $col) {
        echo " - {$col->column_name} ({$col->data_type})\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
