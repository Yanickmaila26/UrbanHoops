<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- ADDING FAC_IVA_PORCENTAJE TO FACTURAS ---\n";

function addCol($conn, $table)
{
    echo "Processing $table on $conn...\n";
    try {
        DB::connection($conn)->statement("ALTER TABLE $table ADD (FAC_IVA_Porcentaje NUMBER(5,2) DEFAULT 15.00)");
        echo "  ✓ Added FAC_IVA_Porcentaje to $table\n";
    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'ORA-01430')) {
            echo "  - Column already exists in $table\n";
        } else {
            echo "  ⚠ Error: " . $e->getMessage() . "\n";
        }
    }
}

// 1. Add to PROD (Master for Facturas)
addCol('oracle', 'FACTURAS');

// 2. Add to COMEE (Replica for Facturas)
addCol('oracle_comee', 'FACTURAS');
