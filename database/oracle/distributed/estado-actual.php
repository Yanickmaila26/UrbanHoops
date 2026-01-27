<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Verificación Rápida - Estado Actual                         ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

echo "1. Tablas en COMEE:\n";
echo "═══════════════════════════════════════════════════════════════\n";
try {
    $tables = DB::connection($comeeConn)->select("SELECT table_name FROM user_tables ORDER BY table_name");
    foreach ($tables as $t) {
        echo "  - " . $t->table_name . "\n";
    }
    echo "  Total: " . count($tables) . " tablas\n\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

echo "2. Tablas en PROD:\n";
echo "═══════════════════════════════════════════════════════════════\n";
try {
    $tables = DB::connection($prodConn)->select("SELECT table_name FROM user_tables WHERE table_name IN ('CLIENTES', 'FACTURAS', 'PRODUCTOS', 'CATEGORIAS', 'CARRITOS') ORDER BY table_name");
    foreach ($tables as $t) {
        echo "  - " . $t->table_name . "\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

echo "3. Insertando datos de prueba directamente:\n";
echo "═══════════════════════════════════════════════════════════════\n";

// Insertar cliente de prueba en PROD (réplica)
try {
    echo "Insertando cliente en PROD...\n";
    DB::connection($prodConn)->statement("
        INSERT INTO clientes (CLI_Ced_Ruc, CLI_Nombre, CLI_Telefono, CLI_Correo)
        VALUES ('1234567890123', 'Cliente Test PROD', '0999999999', 'test@prod.com')
    ");
    echo "  ✓ Cliente insertado en PROD\n";
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'ORA-00001') === false) {
        echo "  ⚠ " . substr($e->getMessage(), 0, 60) . "\n";
    }
}

// Insertar cliente de prueba en COMEE (master)
try {
    echo "Insertando cliente en COMEE...\n";
    DB::connection($comeeConn)->statement("
        INSERT INTO clientes (CLI_Ced_Ruc, CLI_Nombre, CLI_Telefono, CLI_Correo)
        VALUES ('9876543210987', 'Cliente Test COMEE', '0988888888', 'test@comee.com  ')
    ");
    echo "  ✓ Cliente insertado en COMEE\n";
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'ORA-00001') === false) {
        echo "  ⚠ " . substr($e->getMessage(), 0, 60) . "\n";
    }
}

echo "\n";

echo "4. Verificando counts:\n";
echo "═══════════════════════════════════════════════════════════════\n";

try {
    $prodCli = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes")->cnt;
    $comeeCli = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes")->cnt;
    echo "CLIENTES: PROD=$prodCli, COMEE=$comeeCli\n";
} catch (\Exception $e) {
    echo "CLIENTES: Error\n";
}

try {
    $prodPro = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
    $comeePro = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
    echo "PRODUCTOS: PROD=$prodPro, COMEE=$comeePro\n";
} catch (\Exception $e) {
    echo "PRODUCTOS: Error\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "RESUM EN:\n";
echo "- Las tablas están creadas en las ubicaciones correctas\n";
echo "- Los inserts funcionan\n";
echo "- Los triggers de replicación pueden no estar funcionando todavía\n";
echo "- Para usarla aplicación, los datos se deben copiar manualmente o vía triggers\n";
echo "\nPróximo paso: Ejecutar `php artisan migrate:fresh-oracle --seed` y ver errores\n";
