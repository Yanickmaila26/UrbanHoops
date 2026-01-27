<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING ELOQUENT belongsToMany QUERY ===\n\n";

// Enable query logging
DB::enableQueryLog();

$orden = \App\Models\OrdenCompra::where('ORC_Estado', true)->first();
if (!$orden) {
    echo "No active orders.\n";
    exit;
}

echo "Order: {$orden->ORC_Numero}\n";

// Trigger the relation load
$productos = $orden->productos;
echo "Products loaded: " . $productos->count() . "\n\n";

// Show the SQL query that was executed
$queries = DB::getQueryLog();
echo "Queries executed:\n";
foreach ($queries as $q) {
    echo "SQL: " . $q['query'] . "\n";
    echo "Bindings: " . json_encode($q['bindings']) . "\n";
    echo "Time: " . $q['time'] . "ms\n\n";
}

// Now try with explicit eager loading
echo "\n=== TRYING WITH EAGER LOADING ===\n";
DB::flushQueryLog();

$orden2 = \App\Models\OrdenCompra::with('productos')->where('ORC_Estado', true)->first();
$productos2 = $orden2->productos;
echo "Products (eager): " . $productos2->count() . "\n\n";

$queries2 = DB::getQueryLog();
echo "Queries executed:\n";
foreach ($queries2 as $q) {
    echo "SQL: " . $q['query'] . "\n";
    echo "Bindings: " . json_encode($q['bindings']) . "\n\n";
}
