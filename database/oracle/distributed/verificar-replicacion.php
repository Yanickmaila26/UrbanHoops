<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   VERIFICACIÓN FINAL - REPLICACIÓN                             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

echo "CLIENTES (Master en COMEE):\n";
echo "═══════════════════════════════════════════════════════════════\n";
try {
    $comee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes")->cnt;
    $prod = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes")->cnt;

    $status = ($comee == $prod) ? "✓ SINCRONIZADOS" : "⚠ DESINCRONIZADOS";
    echo "COMEE (master): $comee registros\n";
    echo "PROD (réplica):  $prod registros\n";
    echo "$status\n\n";
} catch (\Exception $e) {
    echo "✗ Error: " . substr($e->getMessage(), 0, 100) . "\n\n";
}

echo "PRODUCTOS (Master en PROD):\n";
echo "═══════════════════════════════════════════════════════════════\n";
try {
    $prod = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
    $comee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;

    $status = ($prod == $comee) ? "✓ SINCRONIZADOS" : "⚠ DESINCRONIZADOS";
    echo "PROD (master):   $prod registros\n";
    echo "COMEE (réplica): $comee registros\n";
    echo "$status\n\n";
} catch (\Exception $e) {
    echo "✗ Error: " . substr($e->getMessage(), 0, 100) . "\n\n";
}

echo "FACTURAS (Master en COMEE):\n";
echo "═══════════════════════════════════════════════════════════════\n";
try {
    $comee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM facturas")->cnt;
    $prod = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM facturas")->cnt;

    $status = ($comee == $prod) ? "✓ SINCRONIZADOS" : "⚠ DESINCRONIZADOS";
    echo "COMEE (master): $comee registros\n";
    echo "PROD (réplica):  $prod registros\n";
    echo "$status\n\n";
} catch (\Exception $e) {
    echo "✗ Error: " . substr($e->getMessage(), 0, 100) . "\n\n";
}

echo "TABLAS DISTRIBUIDAS (Solo en COMEE):\n";
echo "═══════════════════════════════════════════════════════════════\n";
try {
    $carritos = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM carritos")->cnt;
    echo "CARRITOS: $carritos registros\n";
} catch (\Exception $e) {
    echo "CARRITOS: Error\n";
}

try {
    $pedidos = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM pedidos")->cnt;
    echo "PEDIDOS: $pedidos registros\n";
} catch (\Exception $e) {
    echo "PEDIDOS: Error\n";
}

echo "\n";

if ($comee == $prod && $prod ==  $comee) {
    echo "✅ ¡REPLICACIÓN FUNCIONANDO CORRECTAMENTE!\n";
} else {
    echo "⚠ Hay problemas de sincronización. Los triggers pueden no estar activos.\n";
    echo "Ejecuta los triggers con:\n";
    echo "  php database/oracle/distributed/ejecutar-script.php 05_triggers_replication_prod.sql\n";
    echo "  php database/oracle/distributed/ejecutar-script.php 06_triggers_replication_comee.sql\n";
}
