<?php

/**
 * Script para diagnosticar y arreglar la distribución de base de datos
 * Uso: php database/oracle/distributed/arreglar-distribucion.php
 */

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Diagnóstico y Corrección de Base de Datos Distribuida       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

echo "🔍 Diagnosticando estado actual...\n\n";

// Verificar qué tablas existen físicamente en PROD
echo "TABLAS FÍSICAS EN PROD:\n";
echo "═══════════════════════════════════════\n";

$prodTables = DB::connection($prodConn)->select("
    SELECT table_name, num_rows 
    FROM user_tables 
    WHERE table_name IN ('CLIENTES', 'CARRITOS', 'DETALLE_CARRITO', 'PEDIDOS', 'DATOS_FACTURACION', 'PRODUCTOS', 'FACTURAS', 'DETALLE_FACTURA')
    ORDER BY table_name
");

$tablasAEliminar = [];

foreach ($prodTables as $table) {
    $name = $table->table_name;
    $rows = $table->num_rows ?? 0;

    if (in_array($name, ['CLIENTES', 'CARRITOS', 'DETALLE_CARRITO', 'PEDIDOS', 'DATOS_FACTURACION'])) {
        echo "  ⚠ $name ($rows registros) - DEBE ELIMINARSE (solo debe estar en COMEE)\n";
        $tablasAEliminar[] = $name;
    } else {
        echo "  ✓ $name ($rows registros) - OK en PROD\n";
    }
}

echo "\n";

// Verificar sinónimos
echo "SINÓNIMOS EN PROD:\n";
echo "═══════════════════════════════════════\n";

$synonyms = DB::connection($prodConn)->select("
    SELECT synonym_name, table_owner, table_name, db_link
    FROM user_synonyms
    WHERE synonym_name IN ('CLIENTES', 'CARRITOS', 'DETALLE_CARRITO', 'PEDIDOS', 'DATOS_FACTURACION')
    ORDER BY synonym_name
");

foreach ($synonyms as $syn) {
    echo "  ✓ {$syn->synonym_name} -> {$syn->table_owner}.{$syn->table_name}@{$syn->db_link}\n";
}

if (empty($synonyms)) {
    echo "  ⚠ No se encontraron sinónimos\n";
}

echo "\n";

// Verificar triggers de replicación
echo "TRIGGERS DE REPLICACIÓN EN PROD:\n";
echo "═══════════════════════════════════════\n";

$triggers = DB::connection($prodConn)->select("
    SELECT trigger_name, status, table_name
    FROM user_triggers
    WHERE trigger_name LIKE '%REPL%'
    ORDER BY trigger_name
");

foreach ($triggers as $trg) {
    $statusIcon = $trg->status === 'ENABLED' ? '✓' : '✗';
    echo "  $statusIcon {$trg->trigger_name} on {$trg->table_name} - {$trg->status}\n";
}

if (empty($triggers)) {
    echo "  ⚠ No se encontraron triggers de replicación\n";
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Preguntar si proceder con la corrección
if (!empty($tablasAEliminar)) {
    echo "⚠ ACCIÓN REQUERIDA:\n";
    echo "Las siguientes tablas existen físicamente en PROD pero NO deberían:\n";
    foreach ($tablasAEliminar as $tabla) {
        echo "  - $tabla\n";
    }
    echo "\n";
    echo "Estas tablas impiden que los sinónimos funcionen correctamente.\n";
    echo "Se procederá a:\n";
    echo "  1. Hacer DROP de estas tablas en PROD\n";
    echo "  2. Los sinónimos ya existen y apuntarán a COMEE\n";
    echo "  3. Verificar que los datos estén en COMEE\n";
    echo "\n";

    echo "¿Proceder? (escribe 'SI' para confirmar): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim($line) !== 'SI') {
        echo "\nOperación cancelada.\n";
        exit(0);
    }

    echo "\n";
    echo "════════════════════════════════════════════════════════════════\n";
    echo "Eliminando tablas de PROD...\n";
    echo "════════════════════════════════════════════════════════════════\n\n";

    foreach ($tablasAEliminar as $tabla) {
        try {
            echo "Eliminando tabla $tabla de PROD...\n";
            DB::connection($prodConn)->statement("DROP TABLE $tabla CASCADE CONSTRAINTS");
            echo "  ✓ Tabla $tabla eliminada\n\n";
        } catch (\Exception $e) {
            echo "  ⚠ Error: " . $e->getMessage() . "\n\n";
        }
    }

    echo "════════════════════════════════════════════════════════════════\n";
    echo "Verificación post-eliminación\n";
    echo "════════════════════════════════════════════════════════════════\n\n";

    // Verificar que los sinónimos funcionan
    echo "Probando acceso vía sinónimos...\n";

    try {
        $count = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes");
        echo "  ✓ Acceso a CLIENTES vía sinónimo: {$count->cnt} registros\n";
    } catch (\Exception $e) {
        echo "  ✗ Error accediendo a CLIENTES: " . $e->getMessage() . "\n";
    }

    try {
        $count = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos");
        echo "  ✓ Acceso a PRODUCTOS: {$count->cnt} registros\n";
    } catch (\Exception $e) {
        echo "  ✗ Error accediendo a PRODUCTOS: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "Verificando triggers de replicación\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Test de replicación de productos
echo "Probando replicación de PRODUCTOS...\n";

try {
    // Contar antes
    $beforeProd = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
    $beforeComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;

    echo "  Antes - PROD: $beforeProd, COMEE: $beforeComee\n";

    if ($beforeProd != $beforeComee) {
        echo "  ⚠ Los counts no coinciden. Esto puede indicar que los triggers no están funcionando.\n";
        echo "  Se necesita re-sincronizar...\n\n";

        echo "¿Deseas copiar todos los productos de PROD a COMEE? (escribe 'SI'): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim($line) === 'SI') {
            echo "\nSincronizando productos...\n";

            // Deshabilitar triggers
            try {
                DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_insert_repl DISABLE");
                DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_update_repl DISABLE");
                DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_delete_repl DISABLE");
            } catch (\Exception $e) {
            }

            // Limpiar y copiar
            DB::connection($comeeConn)->statement("DELETE FROM productos");
            DB::connection($prodConn)->statement("INSERT INTO productos@link_comee SELECT * FROM productos");

            // Reactivar triggers
            try {
                DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_insert_repl ENABLE");
                DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_update_repl ENABLE");
                DB::connection($comeeConn)->statement("ALTER TRIGGER trg_productos_delete_repl ENABLE");
            } catch (\Exception $e) {
            }

            $afterComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
            echo "  ✓ Sincronización completada. COMEE ahora tiene $afterComee productos\n";
        }
    } else {
        echo "  ✓ Los counts coinciden\n";
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Diagnóstico completado                                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "RESUMEN:\n";
echo "  - Tablas en COMEE: clientes, carritos, pedidos, datos_facturacion\n";
echo "  - Tablas en PROD: productos, facturas (master)\n";
echo "  - Réplicas en COMEE: productos, facturas\n";
echo "  - Sinónimos en PROD apuntan a COMEE para acceso transparente\n\n";

echo "Prueba insertar un cliente desde Laravel:\n";
echo "  php artisan tinker\n";
echo "  >>> \$c = new App\\Models\\Cliente;\n";
echo "  >>> \$c->CLI_Ced_Ruc = '1234567890123';\n";
echo "  >>> \$c->CLI_Nombre = 'Test';\n";
echo "  >>> \$c->CLI_Telefono = '0999999999';\n";
echo "  >>> \$c->CLI_Correo = 'test@test.com';\n";
echo "  >>> \$c->save();\n\n";
