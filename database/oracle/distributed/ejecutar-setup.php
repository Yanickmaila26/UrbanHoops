<?php

/**
 * Script directo para ejecutar la configuración de base de datos distribuida
 * Uso: php database/oracle/distributed/ejecutar-setup.php
 */

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   UrbanHoops - Configuración de Base de Datos Distribuida     ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

// Función helper para ejecutar scripts SQL
function ejecutarScript($connection, $scriptPath, $descripcion)
{
    echo "📄 $descripcion...\n";

    if (!file_exists($scriptPath)) {
        echo "  ✗ Archivo no encontrado: $scriptPath\n";
        return false;
    }

    try {
        $sql = file_get_contents($scriptPath);

        // Limpiar comandos SQL*Plus
        $sql = preg_replace('/^\s*PROMPT.*$/m', '', $sql);
        $sql = preg_replace('/^\s*EXIT.*;?$/mi', '', $sql);
        $sql = preg_replace('/^\s*PAUSE.*$/mi', '', $sql);
        $sql = preg_replace('/^\s*SET\s+\w+.*$/m', '', $sql);

        // Dividir por separador / en su propia línea
        $rawBlocks = preg_split('/^\s*\/\s*$/m', $sql);

        $statements = [];

        foreach ($rawBlocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            // Si el bloque no es PL/SQL, dividir por ;
            if (!preg_match('/^\s*(BEGIN|DECLARE|CREATE\s+(OR\s+REPLACE\s+)?(TRIGGER|PROCEDURE|FUNCTION|PACKAGE))/i', $block)) {
                // Es SQL, dividir por ;
                $sqlLines = explode(';', $block);
                foreach ($sqlLines as $line) {
                    $line = trim($line);
                    // Remover comentarios
                    $line = preg_replace('/--.*$/m', '', $line);
                    $line = trim($line);
                    if (!empty($line) && strlen($line) > 5) {
                        $statements[] = $line;
                    }
                }
            } else {
                // Es PL/SQL, guardar como bloque completo
                $block = preg_replace('/--.*$/m', '', $block);
                $block = trim($block);
                if (!empty($block)) {
                    $statements[] = $block;
                }
            }
        }

        // Ejecutar cada statement
        $executed = 0;
        $skipped = 0;

        foreach ($statements as $stmt) {
            if (empty(trim($stmt))) continue;

            try {
                DB::connection($connection)->statement($stmt);
                $executed++;
            } catch (\Exception $e) {
                $errMsg = $e->getMessage();

                // Ignorar errores esperados
                if (
                    strpos($errMsg, 'ORA-00955') !== false ||  // Objeto ya existe
                    strpos($errMsg, 'ORA-00942') !== false ||  // Tabla no existe
                    strpos($errMsg, 'ORA-01434') !== false ||  // Sinónimo no existe  
                    strpos($errMsg, 'ORA-04043') !== false
                ) {  // Objeto no existe
                    $skipped++;
                    continue;
                }

                // Mostrar error pero continuar
                echo "  ⚠ Advertencia: " . substr($errMsg, 0, 80) . "...\n";
                $skipped++;
            }
        }

        echo "  ✓ $descripcion completado ($executed OK, $skipped ignorados)\n\n";
        return true;
    } catch (\Exception $e) {
        echo "  ✗ Error fatal: " . $e->getMessage() . "\n\n";
        return false;
    }
}

// Verificar prerequisitos
echo "🔍 Verificando prerequisitos...\n\n";

try {
    DB::connection($prodConn)->select('SELECT 1 FROM dual');
    echo "✓ Conexión a PROD exitosa\n";
} catch (\Exception $e) {
    echo "✗ Error conectando a PROD: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    DB::connection($comeeConn)->select('SELECT 1 FROM dual');
    echo "✓ Conexión a COMEE exitosa\n";
} catch (\Exception $e) {
    echo "✗ Error conectando a COMEE: " . $e->getMessage() . "\n";
    exit(1);
}

try {
    DB::connection($prodConn)->select('SELECT 1 FROM dual@link_comee');
    echo "✓ Database link PROD -> COMEE funcionando\n";
} catch (\Exception $e) {
    echo "✗ Database link link_comee no funciona\n";
    echo "  Crea el database link en PROD:\n";
    echo "  CREATE DATABASE LINK link_comee CONNECT TO u_comee IDENTIFIED BY secreto123 USING 'comee';\n";
    exit(1);
}

try {
    DB::connection($comeeConn)->select('SELECT 1 FROM dual@link_prod');
    echo "✓ Database link COMEE -> PROD funcionando\n";
} catch (\Exception $e) {
    echo "✗ Database link link_prod no funciona\n";
    echo "  Crea el database link en COMEE:\n";
    echo "  CREATE DATABASE LINK link_prod CONNECT TO u_prod IDENTIFIED BY secreto123 USING 'prod';\n";
    exit(1);
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "FASE 1: Configurando COMEE PDB\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$baseDir = __DIR__;

ejecutarScript($comeeConn, "$baseDir/01_create_tables_comee.sql", "Creando tablas principales en COMEE");
ejecutarScript($comeeConn, "$baseDir/02_create_replica_tables_comee.sql", "Creando tablas de réplica en COMEE");
ejecutarScript($comeeConn, "$baseDir/03_create_synonyms_comee.sql", "Creando sinónimos en COMEE");

echo "═══════════════════════════════════════════════════════════════\n";
echo "FASE 2: Configurando PROD PDB\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

ejecutarScript($prodConn, "$baseDir/04_create_synonyms_prod.sql", "Creando sinónimos en PROD");
ejecutarScript($prodConn, "$baseDir/05_triggers_replication_prod.sql", "Creando triggers de replicación en PROD");

echo "═══════════════════════════════════════════════════════════════\n";
echo "FASE 3: Completando COMEE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

ejecutarScript($comeeConn, "$baseDir/06_triggers_replication_comee.sql", "Creando triggers de replicación en COMEE");

echo "═══════════════════════════════════════════════════════════════\n";
echo "FASE 4: Migración de Datos\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "⚠ Deshabilitando triggers temporalmente...\n";

$triggers = [
    'trg_productos_insert_repl',
    'trg_productos_update_repl',
    'trg_productos_delete_repl',
    'trg_facturas_insert_repl',
    'trg_detalle_factura_insert_repl',
];

foreach ($triggers as $trigger) {
    try {
        DB::connection($comeeConn)->statement("ALTER TRIGGER $trigger DISABLE");
    } catch (\Exception $e) {
        // Trigger no existe, continuar
    }
}

echo "✓ Triggers deshabilitados\n\n";

ejecutarScript($prodConn, "$baseDir/07_migrate_data.sql", "Migrando datos de PROD a COMEE");

echo "⚠ Reactivando triggers...\n";

foreach ($triggers as $trigger) {
    try {
        DB::connection($comeeConn)->statement("ALTER TRIGGER $trigger ENABLE");
    } catch (\Exception $e) {
        echo "  ⚠ No se pudo habilitar $trigger\n";
    }
}

echo "✓ Triggers reactivados\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "FASE 5: Verificación\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📋 Verificando configuración...\n\n";

try {
    $count = DB::connection($prodConn)->selectOne('SELECT COUNT(*) as cnt FROM clientes');
    echo "✓ Acceso a clientes (via sinónimo): {$count->cnt} registros\n";
} catch (\Exception $e) {
    echo "✗ Error accediendo a clientes: " . substr($e->getMessage(), 0, 100) . "...\n";
}

try {
    $count = DB::connection($prodConn)->selectOne('SELECT COUNT(*) as cnt FROM productos');
    echo "✓ Acceso a productos: {$count->cnt} registros\n";
} catch (\Exception $e) {
    echo "✗ Error accediendo a productos: " . substr($e->getMessage(), 0, 100) . "...\n";
}

try {
    $count = DB::connection($comeeConn)->selectOne('SELECT COUNT(*) as cnt FROM productos');
    echo "✓ Réplica de productos en COMEE: {$count->cnt} registros\n";
} catch (\Exception $e) {
    echo "✗ Error accediendo a réplica de productos: " . substr($e->getMessage(), 0, 100) . "...\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   ✅ Configuración Distribuida Completada!                     ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "Próximos pasos:\n";
echo "  1. Ejecuta: php artisan migrate:fresh-oracle --seed\n";
echo "  2. Verifica que las tablas están distribuidas correctamente\n";
echo "  3. Prueba la aplicación\n\n";

echo "Para más detalles, consulta:\n";
echo "  database/oracle/distributed/README.md\n\n";
