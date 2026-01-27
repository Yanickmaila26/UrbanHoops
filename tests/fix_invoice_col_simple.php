<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- Applying Fix: Add DFC_TALLA to DETALLE_FACTURA (COMEE) ---\n";

try {
    // Try via Link from PROD (most likely path)
    DB::connection('oracle')->statement("ALTER TABLE u_comee.DETALLE_FACTURA@link_comee ADD (DFC_TALLA VARCHAR2(50))");
    echo "SUCCESS: Column added to COMEE via link.\n";
} catch (\Exception $e) {
    $msg = $e->getMessage();
    if (str_contains($msg, 'ORA-01430')) {
        echo "INFO: Column already exists in COMEE.\n";
    } else {
        echo "ERROR (Link): " . explode("\n", $msg)[0] . "\n";

        // Try Direct Connection if Link fails
        try {
            echo "Retrying via Direct Connection to COMEE...\n";
            // Check if oracle_comee connection is defined in config
            if (config('database.connections.oracle_comee')) {
                DB::connection('oracle_comee')->statement("ALTER TABLE DETALLE_FACTURA ADD (DFC_TALLA VARCHAR2(50))");
                echo "SUCCESS: Column added via Direct Connection.\n";
            } else {
                echo "WARN: No oracle_comee connection configured.\n";
            }
        } catch (\Exception $e2) {
            $msg2 = $e2->getMessage();
            if (str_contains($msg2, 'ORA-01430')) {
                echo "INFO: Column already exists (Direct).\n";
            } else {
                echo "ERROR (Direct): " . explode("\n", $msg2)[0] . "\n";
            }
        }
    }
}
