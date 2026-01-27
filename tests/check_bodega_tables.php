<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- CHECKING BODEGA TABLES IN PROD ---\n";

$tables = ['BODEGAS', 'PRODUCTO_BODEGA'];

foreach ($tables as $table) {
    $obj = DB::connection('oracle')->selectOne("
        SELECT object_type 
        FROM user_objects 
        WHERE object_name = ?
    ", [$table]);

    if ($obj) {
        echo "$table exists as " . $obj->object_type . "\n";
    } else {
        echo "$table NOT FOUND in PROD.\n";
    }
}
