<?php

/**
 * Setup completo de base de datos distribuida - VERSIÓN CORREGIDA
 * 
 * ARQUITECTURA:
 * - Tablas SOLO en COMEE: carritos, detalle_carrito, detalle_factura, pedidos, datos_facturacion
 * - Tablas REPLICADAS:
 *   * clientes: MASTER en COMEE, réplica en PROD
 *   * facturas: MASTER en COMEE, réplica en PROD  
 *   * productos: MASTER en PROD, réplica en COMEE
 * 
 * Uso: php database/oracle/distributed/setup-completo.php
 */

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Setup Completo - Base de Datos Distribuida v2.0             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

echo "⚠️  ARQUITECTURA:\n";
echo "  SOLO en COMEE: carritos, detalle_carrito, detalle_factura, pedidos\n";
echo "  REPLICADAS:\n";
echo "    - clientes:  MASTER en COMEE, réplica en PROD\n";
echo "    - facturas:  MASTER en COMEE, réplica en PROD\n";
echo "    - productos: MASTER en PROD, réplica en COMEE\n\n";

echo "Este script va a:\n";
echo "  1. DROP todas las tablas en PROD y COMEE\n";
echo "  2. Crear todas las tablas en la configuración distribuida correcta\n";
echo "  3. Configurar triggers de replicación bi-direccional\n";
echo "  4. Ejecutar seeders (solo poblan MASTERS, triggers replican automáticamente)\n\n";

echo "¿Continuar? (escribe 'SI'): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim($line) !== 'SI') {
    echo "\nOperación cancelada.\n";
    exit(0);
}

function ejecutarSQL($connection, $file, $desc)
{
    echo "$desc...\n";

    if (!file_exists($file)) {
        echo "  ⚠ Archivo no encontrado: $file\n";
        return false;
    }

    $sql = file_get_contents($file);
    $sql = preg_replace('/^\s*PROMPT.*$/m', '', $sql);
    $sql = preg_replace('/^\s*EXIT.*;?$/mi', '', $sql);
    $sql = preg_replace('/^\s*SET\s+\w+.*$/m', '', $sql);

    $rawBlocks = preg_split('/^\s*\/\s*$/m', $sql);

    $errors = 0;
    foreach ($rawBlocks as $block) {
        $block = trim($block);
        if (empty($block) || strlen($block) < 10) continue;

        try {
            DB::connection($connection)->statement($block);
        } catch (\Exception $e) {
            if (
                strpos($e->getMessage(), 'ORA-00955') === false &&
                strpos($e->getMessage(), 'ORA-00942') === false &&
                strpos($e->getMessage(), 'ORA-01434') === false &&
                strpos($e->getMessage(), 'ORA-02291') === false
            ) {
                echo "  ⚠ " . substr($e->getMessage(), 0, 100) . "\n";
                $errors++;
            }
        }
    }

    echo "  ✓ Completado" . ($errors > 0 ? " ($errors advertencias)" : "") . "\n\n";
    return true;
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 1: Limpiando ambas bases de datos\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Limpiar PROD
echo "Limpiando PROD...\n";
try {
    DB::connection($prodConn)->statement("
        BEGIN
            FOR c IN (SELECT table_name FROM user_tables WHERE secondary = 'N') LOOP
                EXECUTE IMMEDIATE ('DROP TABLE \"' || c.table_name || '\" CASCADE CONSTRAINTS');
            END LOOP;
            
            FOR syn IN (SELECT synonym_name FROM user_synonyms) LOOP
                EXECUTE IMMEDIATE ('DROP SYNONYM ' || syn.synonym_name);
            END LOOP;
        END;
    ");
    echo "  ✓ PROD limpiado\n\n";
} catch (\Exception $e) {
    echo "  ⚠ Error: " . $e->getMessage() . "\n\n";
}

// Limpiar COMEE  
echo "Limpiando COMEE...\n";
try {
    DB::connection($comeeConn)->statement("
        BEGIN
            FOR c IN (SELECT table_name FROM user_tables WHERE secondary = 'N') LOOP
                EXECUTE IMMEDIATE ('DROP TABLE \"' || c.table_name || '\" CASCADE CONSTRAINTS');
            END LOOP;
            
            FOR syn IN (SELECT synonym_name FROM user_synonyms) LOOP
                EXECUTE IMMEDIATE ('DROP SYNONYM ' || syn.synonym_name);
            END LOOP;
        END;
    ");
    echo "  ✓ COMEE limpiado\n\n";
} catch (\Exception $e) {
    echo "  ⚠ Error: " . $e->getMessage() . "\n\n";
}

$baseDir = __DIR__;

echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 2: Creando tablas base con migraciones en PROD\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Ejecutar migraciones pero SIN confirmar para evitar prompts
// Usamos passt hru con input automático
$descriptorspec = [
    0 => ["pipe", "r"],
    1 => ["pipe", "w"],
    2 => ["pipe", "w"]
];

$process = proc_open('php artisan migrate:fresh-oracle', $descriptorspec, $pipes, getcwd());

if (is_resource($process)) {
    fwrite($pipes[0], "yes\n");
    fclose($pipes[0]);

    echo stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $return = proc_close($process);

    if ($return !== 0) {
        echo "\n⚠ Advertencia en migraciones, continuando...\n\n";
    } else {
        echo "\n✓ Migraciones en PROD completadas\n\n";
    }
}

echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 3: Creando tablas en COMEE\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Crear sinónimo para CATEGORIAS (necesario para FK de productos)
echo "Creando sinónimo para CATEGORIAS en COMEE...\n";
try {
    DB::connection($comeeConn)->statement("CREATE SYNONYM CATEGORIAS FOR u_prod.CATEGORIAS@link_prod");
    echo "  ✓ Sinónimo CATEGORIAS creado\n\n";
} catch (\Exception $e) {
    echo "  ⚠ " . substr($e->getMessage(), 0, 100) . "\n\n";
}

// Crear las tablas replicadas y distribuidas
ejecutarSQL($comeeConn, "$baseDir/02_create_replica_tables_comee.sql", "Creando tablas REPLICADAS en COMEE");
ejecutarSQL($comeeConn, "$baseDir/01_create_tables_comee.sql", "Creando tablas DISTRIBUIDAS en COMEE");

echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 4: Creando tablas REPLICA en PROD\n";
echo "════════════════════════════════════════════════════════════════\n\n";

ejecutarSQL($prodConn, "$baseDir/02B_create_replica_tables_prod.sql", "Creando réplicas de COMEE en PROD");

echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 5: Creando sinónimos\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Sinónimos en PROD para tablas SOLO en COMEE
$tablasComee = ['CARRITOS', 'DETALLE_CARRITO', 'DETALLE_FACTURA', 'PEDIDOS', 'DATOS_FACTURACION'];

echo "Creando sinónimos en PROD para tablas de COMEE...\n";
foreach ($tablasComee as $tabla) {
    try {
        DB::connection($prodConn)->statement("CREATE SYNONYM $tabla FOR u_comee.$tabla@link_comee");
        echo "  ✓ $tabla\n";
    } catch (\Exception $e) {
        echo "  ⚠ $tabla: " . substr($e->getMessage(), 0, 60) . "\n";
    }
}

echo "\n";

// Sinónimos en COMEE para tablas de PROD
echo "Creando sinónimos en COMEE para tablas de PROD...\n";
$tablasProd = [
    'PROVEEDORS',
    'BODEGAS',
    'KARDEXES',
    'ORDEN_COMPRAS',
    'TRANSACCIONS',
    'SUBCATEGORIAS',
    'USUARIO_APLICACIONS',
    'USERS'
];

foreach ($tablasProd as $tabla) {
    try {
        DB::connection($comeeConn)->statement("CREATE SYNONYM $tabla FOR u_prod.$tabla@link_prod");
        echo "  ✓ $tabla\n";
    } catch (\Exception $e) {
        // Ignorar si no existe
    }
}

echo "\n";

echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 6: Configurando triggers de replicación\n";
echo "════════════════════════════════════════════════════════════════\n\n";

ejecutarSQL($prodConn, "$baseDir/05_triggers_replication_prod.sql", "Triggers en PROD");
ejecutarSQL($comeeConn, "$baseDir/06_triggers_replication_comee.sql", "Triggers en COMEE");

echo "════════════════════════════════════════════════════════════════\n";
echo "Paso 7: Ejecutando seeders\n";
echo "════════════════════════════════════════════════════════════════\n\n";

echo "Los seeders poblarán las tablas MASTER:\n";
echo "  - clientes/facturas en COMEE (triggers replican a PROD)\n";
echo "  - productos en PROD (triggers replican a COMEE)\n\n";

passthru('php artisan db:seed', $return);

if ($return === 0) {
    echo "\n✓ Seeders completados\n\n";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   ✅ Setup Completo Finalizado!                                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "VERIFICACIÓN:\n";
echo "═══════════════════════════════════════════════════════════════\n";

try {
    $cliComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes")->cnt;
    $cliProd = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes")->cnt;
    echo "  Clientes: COMEE=$cliComee (master), PROD=$cliProd (réplica)\n";
} catch (\Exception $e) {
    echo "  ✗ Error con clientes\n";
}

try {
    $proProd = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
    $proComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos")->cnt;
    echo "  Productos: PROD=$proProd (master), COMEE=$proComee (réplica)\n";
} catch (\Exception $e) {
    echo "  ✗ Error con productos\n";
}

try {
    $facComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM facturas")->cnt;
    $facProd = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM facturas")->cnt;
    echo "  Facturas: COMEE=$facComee (master), PROD=$facProd (réplica)\n";
} catch (\Exception $e) {
    echo "  ✗ Error con facturas\n";
}

echo "\n✅ Todo listo! Usa la aplicación normalmente.\n";
echo "Los datos se distribuyen y replican automáticamente.\n";
