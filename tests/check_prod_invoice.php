<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- CHECKING DETALLE_FACTURA IN PROD ---\n";

// 1. Check Object Type in PROD
$obj = DB::connection('oracle')->selectOne("
    SELECT object_type 
    FROM user_objects 
    WHERE object_name = 'DETALLE_FACTURA'
");

if ($obj) {
    echo "Object Type: " . $obj->object_type . "\n";

    if ($obj->object_type === 'TABLE') {
        echo "WARNING: DETALLE_FACTURA is a PHYSICAL TABLE in PROD.\n";
        // Check columns
        $cols = DB::connection('oracle')->select("
            SELECT column_name 
            FROM user_tab_columns 
            WHERE table_name = 'DETALLE_FACTURA'
        ");
        echo "Columns:\n";
        $hasTalla = false;
        foreach ($cols as $col) {
            echo " - " . $col->column_name . "\n";
            if ($col->column_name === 'DFC_TALLA') $hasTalla = true;
        }

        if (!$hasTalla) {
            echo "MISSING DFC_TALLA in PROD physical table.\n";
            echo "Attempting to fix by dropping table and creating synonym...\n";
            // Dropping table logic (Careful!)
        }
    } elseif ($obj->object_type === 'SYNONYM') {
        echo "Object is a SYNONYM. Checking target...\n";
        $syn = DB::connection('oracle')->selectOne("
            SELECT table_owner, table_name, db_link 
            FROM user_synonyms 
            WHERE synonym_name = 'DETALLE_FACTURA'
        ");
        print_r($syn);

        // Check target columns via link
        try {
            $cols = DB::connection('oracle')->select("
                SELECT column_name FROM all_tab_columns@link_comee 
                WHERE table_name = 'DETALLE_FACTURA' AND owner = 'U_COMEE'
            ");
            echo "Target Columns in COMEE:\n";
            foreach ($cols as $col) {
                echo " - " . $col->column_name . "\n";
            }
        } catch (\Exception $e) {
            echo "Error checking target: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "Object DETALLE_FACTURA NOT FOUND in PROD.\n";
}
