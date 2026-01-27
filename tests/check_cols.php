<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking DETALLE_CARRITO columns in PROD...\n";
$cols = DB::select("SELECT column_name FROM user_tab_columns WHERE table_name = 'DETALLE_CARRITO'"); // Synonyms usually don't show here unless accessed, or we check ALL_TAB_COLUMNS
if (empty($cols)) {
    // try all_tab_columns for synonym target
    echo "Checking ALL_TAB_COLUMNS...\n";
    $cols = DB::select("SELECT column_name FROM all_tab_columns WHERE table_name = 'DETALLE_CARRITO'");
}

foreach ($cols as $c) {
    echo " - " . $c->column_name . "\n";
}
