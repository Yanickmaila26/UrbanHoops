<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$table = 'detalle_ord_com';
echo "Table: $table\n";
$cols = DB::select("SELECT column_name, data_type FROM user_tab_columns WHERE table_name = UPPER('$table')");
foreach ($cols as $col) {
    echo " - " . $col->column_name . " (" . $col->data_type . ")\n";
}
