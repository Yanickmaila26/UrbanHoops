<?php
require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tablesToCheck = ['DATOS_FACTURACION', 'CARRITOS', 'DETALLE_CARRITO', 'DETALLE_FACTURA', 'PEDIDOS'];
$allGood = true;

echo "Verifying Distributed Tables in PROD...\n\n";

foreach ($tablesToCheck as $table) {
    // Check if it's a table
    $isTable = DB::connection('oracle')->selectOne("SELECT COUNT(*) as cnt FROM user_tables WHERE table_name = ?", [$table])->cnt;

    // Check if it's a synonym
    $isSynonym = DB::connection('oracle')->selectOne("SELECT COUNT(*) as cnt FROM user_synonyms WHERE synonym_name = ?", [$table])->cnt;

    echo "Object: $table\n";

    if ($isTable) {
        echo "  [FAIL] Exists as PHYSICAL TABLE in PROD (Should be Synonym)\n";
        $allGood = false;
    } else {
        echo "  [OK] Not a physical table\n";
    }

    if ($isSynonym) {
        $syn = DB::connection('oracle')->selectOne("SELECT db_link FROM user_synonyms WHERE synonym_name = ?", [$table]);
        echo "  [OK] Exists as SYNONYM pointing to {$syn->db_link}\n";
    } else {
        echo "  [FAIL] Missing SYNONYM in PROD\n";
        $allGood = false;
    }
    echo "\n";
}

if ($allGood) {
    echo "✅ SUCCESS: All distributed tables are correctly configured as Synonyms in PROD.\n";
} else {
    echo "❌ FAILURE: Some tables are still physical or missing synonyms.\n";
}
