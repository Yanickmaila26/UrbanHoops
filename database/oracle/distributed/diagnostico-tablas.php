<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "═══════════════════════════════════════════════════════════════\n";
echo "DIAGNÓSTICO - TABLAS EN COMEE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    $tables = DB::connection('oracle_comee')->select("SELECT table_name FROM user_tables ORDER BY table_name");

    if (empty($tables)) {
        echo "⚠ NO HAY TABLAS EN COMEE\n\n";
    } else {
        echo "Tablas encontradas (" . count($tables) . "):\n";
        foreach ($tables as $t) {
            echo "  - " . $t->table_name . "\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "DIAGNÓSTICO - TABLAS EN PROD\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    $tables = DB::connection('oracle')->select("SELECT table_name FROM user_tables WHERE table_name IN ('CLIENTES', 'FACTURAS', 'PRODUCTOS', 'CARRITOS', 'PROVEEDORS', 'CATEGORIAS') ORDER BY table_name");

    echo "Tablas clave encontradas (" . count($tables) . "):\n";
    foreach ($tables as $t) {
        echo "  - " . $t->table_name . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
