<?php

/**
 * Script para probar y arreglar los triggers de replicación
 */

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Test y Corrección de Triggers de Replicación                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

// 1. Verificar estado de triggers
echo "1. Verificando triggers de replicación en PROD...\n";
echo "═══════════════════════════════════════════════════════════════\n";

$triggers = DB::connection($prodConn)->select("
    SELECT trigger_name, status, table_name
    FROM user_triggers
    WHERE trigger_name LIKE '%REPL%'
    ORDER BY trigger_name
");

foreach ($triggers as $trg) {
    $icon = $trg->status === 'ENABLED' ? '✓' : '✗';
    echo "  $icon {$trg->trigger_name} ({$trg->table_name}) - {$trg->status}\n";
}

if (empty($triggers)) {
    echo "  ⚠ No hay triggers de replicación en PROD\n";
}

echo "\n";

// 2. Verificar estado en COMEE
echo "2. Verificando triggers de replicación en COMEE...\n";
echo "═══════════════════════════════════════════════════════════════\n";

$triggersComee = DB::connection($comeeConn)->select("
    SELECT trigger_name, status, table_name
    FROM user_triggers
    WHERE trigger_name LIKE '%REPL%'
    ORDER BY trigger_name
");

foreach ($triggersComee as $trg) {
    $icon = $trg->status === 'ENABLED' ? '✓' : '✗';
    echo "  $icon {$trg->trigger_name} ({$trg->table_name}) - {$trg->status}\n";
}

if (empty($triggersComee)) {
    echo "  ⚠ No hay triggers de replicación en COMEE\n";
}

echo "\n";

// 3. Comparar counts de productos
echo "3. Comparando counts de productos...\n";
echo "═══════════════════════════════════════════════════════════════\n";

$prodCount = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
$comeeCount = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;

echo "  PROD:  $prodCount productos\n";
echo "  COMEE: $comeeCount productos\n";

if ($prodCount != $comeeCount) {
    echo "\n  ⚠ Los counts NO coinciden. Los triggers no están funcionando o hay desincronización.\n";
    echo "  ¿Deseas sincronizar COMEE con PROD? (escribe 'SI'): ";

    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim($line) === 'SI') {
        echo "\n  Sincronizando...\n";

        // Deshabilitar triggers
        try {
            DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_insert_repl DISABLE");
            DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_update_repl DISABLE");
            DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_delete_repl DISABLE");
            echo "  ✓ Triggers deshabilitados en COMEE\n";
        } catch (\Exception $e) {
            echo "  ⚠ Error deshabilitando triggers: " . $e->getMessage() . "\n";
        }

        try {
            // Limpiar COMEE
            DB::connection($comeeConn)->statement("DELETE FROM productos");
            echo "  ✓ Productos eliminados de COMEE\n";

            // Copiar de PROD
            DB::connection($comeeConn)->statement("INSERT INTO productos SELECT * FROM productos@link_prod");
            echo "  ✓ Productos copiados de PROD a COMEE\n";

            $newCount = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
            echo "  ✓ COMEE ahora tiene $newCount productos\n";
        } catch (\Exception $e) {
            echo "  ✗ Error durante sincronización: " . $e->getMessage() . "\n";
        }

        // Reactivar triggers
        try {
            DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_insert_repl ENABLE");
            DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_update_repl ENABLE");
            DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_delete_repl ENABLE");
            echo "  ✓ Triggers reactivados en COMEE\n";
        } catch (\Exception $e) {
            echo "  ⚠ Error reactivando triggers: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "\n  ✓ Los counts coinciden, productos están sincronizados\n";
}

echo "\n";

// 4. Probar replicación con INSERT
echo "4. Probando replicación de productos con INSERT...\n";
echo "═══════════════════════════════════════════════════════════════\n";

try {
    $testCode = 'TEST-REPL-' . time();

    // Limpiar si existe
    DB::connection($prodConn)->statement("DELETE FROM productos WHERE PRO_Codigo = '$testCode'");
    DB::connection($comeeConn)->statement("DELETE FROM productos WHERE PRO_Codigo = '$testCode'");

    echo "  Insertando producto de prueba en PROD...\n";
    DB::connection($prodConn)->statement("
        INSERT INTO productos (PRO_Codigo, PRO_Nombre, PRO_Descripcion, PRO_Precio, PRO_Stock, PRO_Color, PRO_Marca, ACTIVO)
        VALUES ('$testCode', 'Producto Test', 'Test replicacion', 100.00, 10, 'Negro', 'Generica', 1)
    ");
    echo "  ✓ Producto insertado en PROD\n";

    sleep(2); // Esperar un momento para que el trigger se ejecute

    $enProd = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos WHERE PRO_Codigo = '$testCode'")->cnt;
    $enComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos WHERE PRO_Codigo = '$testCode'")->cnt;

    echo "  En PROD:  $enProd\n";
    echo "  En COMEE: $enComee\n";

    if ($enComee == 1) {
        echo "\n  ✅ REPLICACIÓN FUNCIONANDO! El producto se replicó a COMEE.\n";
    } else {
        echo "\n  ✗ REPLICACIÓN NO FUNCIONA. El producto NO se replicó a COMEE.\n";
        echo "  Los triggers de replicación pueden no estar funcionando correctamente.\n";
    }

    // Limpiar
    DB::connection($prodConn)->statement("DELETE FROM productos WHERE PRO_Codigo = '$testCode'");
    DB::connection($comeeConn)->statement("DELETE FROM productos WHERE PRO_Codigo = '$testCode'");
} catch (\Exception $e) {
    echo "  ✗ Error en test de replicación: " . $e->getMessage() . "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Test de replicación completado                               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "RESUMEN DE LA CONFIGURACIÓN:\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "TABLAS SOLO EN COMEE (acceso desde PROD vía sinónimos):\n";
echo "  - clientes\n";
echo "  - carritos\n";
echo "  - detalle_carrito\n";
echo "  - pedidos\n";
echo "  - datos_facturacion\n\n";

echo "TABLAS EN PROD (master con réplica en COMEE):\n";
echo "  - productos (replicación bi-direccional)\n";
echo "  - facturas (replicación INSERT only)\n";
echo "  - detalle_factura (replicación INSERT only)\n\n";

echo "Para probar desde Laravel:\n";
echo "  php artisan tinker\n";
echo "  >>> \$p = App\\Models\\Producto::first()\n";
echo "  >>> \$p->PRO_Nombre = 'Actualizado'\n";
echo "  >>> \$p->save()\n\n";
