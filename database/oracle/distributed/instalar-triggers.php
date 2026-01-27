<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$logFile = __DIR__ . '/install_log.txt';
file_put_contents($logFile, "INICIO INSTALACION TRIGGERS\n\n");

function logMsg($msg)
{
    global $logFile;
    echo $msg;
    file_put_contents($logFile, $msg, FILE_APPEND);
}

logMsg("----------------------------------------------------------------\n");
logMsg("   Instalando Triggers de Replicacion (Debug Mode)             \n");
logMsg("----------------------------------------------------------------\n\n");

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';
$baseDir = __DIR__;

function ejecutarTriggers($connection, $file, $desc)
{
    logMsg("$desc...\n");

    if (!file_exists($file)) {
        logMsg("  [ERROR] Archivo no encontrado: $file\n");
        return false;
    }

    $sql = file_get_contents($file);
    $sql = preg_replace('/^\s*PROMPT.*$/m', '', $sql);
    $sql = preg_replace('/^\s*EXIT.*;?$/mi', '', $sql);
    $sql = preg_replace('/^\s*SET\s+\w+.*$/m', '', $sql);

    $rawBlocks = preg_split('/^\s*\/\s*$/m', $sql);

    $success = true;

    foreach ($rawBlocks as $block) {
        $block = trim($block);
        if (empty($block) || strlen($block) < 20) continue;

        preg_match('/CREATE\s+OR\s+REPLACE\s+TRIGGER\s+(\w+)/i', $block, $matches);
        $triggerName = $matches[1] ?? 'Desconocido';

        try {
            DB::connection($connection)->statement($block);

            $errors = DB::connection($connection)->select("SELECT * FROM user_errors WHERE name = UPPER(?) AND type = 'TRIGGER' ORDER BY sequence", [$triggerName]);

            if (!empty($errors)) {
                logMsg("  [WARN] Advertencia en trigger $triggerName:\n");
                foreach ($errors as $err) {
                    logMsg("     Line {$err->line}: {$err->text}\n");
                }
                $success = false;
            } else {
                // logMsg("  [OK] $triggerName creado correctamente\n");
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'ORA-00955') === false) {
                if (strpos($e->getMessage(), 'ORA-24344') !== false) {
                    logMsg("  [ERROR] Error de Compilacion (ORA-24344) en $triggerName:\n");
                    $errors = DB::connection($connection)->select("SELECT * FROM user_errors WHERE name = UPPER(?) AND type = 'TRIGGER' ORDER BY sequence", [$triggerName]);
                    foreach ($errors as $err) {
                        logMsg("     Line {$err->line}: {$err->text}\n");
                    }
                } else {
                    logMsg("  [ERROR] Error ejecutando bloque para $triggerName: " . substr($e->getMessage(), 0, 100) . "\n");
                }
                $success = false;
            }
        }
    }

    logMsg("  " . ($success ? "[OK] Completado sin errores" : "[WARN] Completado con advertencias") . "\n\n");
    return $success;
}

logMsg("Verificando columnas requeridas en PROD...\n");
try {
    $colsCliente = DB::connection($prodConn)->select("SELECT column_name FROM user_tab_columns WHERE table_name = 'CLIENTES'");
    $colNamesCli = array_map(function ($c) {
        return $c->column_name;
    }, $colsCliente);
    if (!in_array('USUARIO_APLICACION_ID', $colNamesCli)) {
        logMsg("  [ALERTA] Columna USUARIO_APLICACION_ID no encontrada en CLIENTES (PROD). La migracion fallo o no corrio.\n");
    } else {
        logMsg("  [OK] Columna USUARIO_APLICACION_ID existe en CLIENTES\n");
    }

    $colsProd = DB::connection($prodConn)->select("SELECT column_name FROM user_tab_columns WHERE table_name = 'PRODUCTOS'");
    $colNamesProd = array_map(function ($c) {
        return $c->column_name;
    }, $colsProd);
    if (!in_array('SCT_CODIGO', $colNamesProd)) {
        logMsg("  [ALERTA] Columna SCT_CODIGO no encontrada en PRODUCTOS (PROD).\n");
    } else {
        logMsg("  [OK] Columna SCT_CODIGO existe en PRODUCTOS\n");
    }
} catch (\Exception $e) {
    logMsg("  [ERROR] Error verificando columnas: " . $e->getMessage() . "\n");
}
logMsg("\n");


ejecutarTriggers($prodConn, "$baseDir/05_triggers_replication_prod.sql", "Creando triggers en PROD");
ejecutarTriggers($comeeConn, "$baseDir/06_triggers_replication_comee.sql", "Creando triggers en COMEE");

logMsg("----------------------------------------------------------------\n");
logMsg("Verificando triggers instalados\n");
logMsg("----------------------------------------------------------------\n\n");

logMsg("Triggers en PROD:\n");
try {
    $triggers = DB::connection($prodConn)->select("SELECT trigger_name, status FROM user_triggers WHERE trigger_name LIKE '%REPL%' ORDER BY trigger_name");
    foreach ($triggers as $t) {
        if ($t->status !== 'ENABLED') {
            $errors = DB::connection($prodConn)->select("SELECT * FROM user_errors WHERE name = ? AND type = 'TRIGGER'", [$t->trigger_name]);
            logMsg("  [FAIL] {$t->trigger_name} - {$t->status}\n");
            foreach ($errors as $err) {
                logMsg("    -> {$err->text}\n");
            }
        } else {
            logMsg("  [OK] {$t->trigger_name} - {$t->status}\n");
        }
    }
} catch (\Exception $e) {
    logMsg("  [ERROR] Error consultando triggers PROD: " . $e->getMessage() . "\n");
}

logMsg("\nTriggers en COMEE:\n");
try {
    $triggers = DB::connection($comeeConn)->select("SELECT trigger_name, status FROM user_triggers WHERE trigger_name LIKE '%REPL%' ORDER BY trigger_name");
    foreach ($triggers as $t) {
        logMsg("  " . ($t->status === 'ENABLED' ? '[OK]' : '[FAIL]') . " {$t->trigger_name} - {$t->status}\n");
    }
} catch (\Exception $e) {
    logMsg("  [ERROR] Error consultando triggers COMEE: " . $e->getMessage() . "\n");
}

logMsg("\n----------------------------------------------------------------\n");
logMsg("   [OK] Triggers de replicacion instalados                        \n");
logMsg("----------------------------------------------------------------\n\n");

logMsg("PROBANDO REPLICACION (PROD -> COMEE):\n");
logMsg("---------------------------------------------------------------\n\n");

$testCode = 'TEST-' . time();
logMsg("1. Insertando producto en PROD (master)...\n");
try {
    $prvId = '9999999999001';
    // Insertar proveedor dummy si no existe para evitar fallo FK
    DB::connection($prodConn)->statement("
        MERGE INTO proveedors p
        USING (SELECT '$prvId' as PRV_Ced_Ruc FROM dual) s
        ON (p.PRV_Ced_Ruc = s.PRV_Ced_Ruc)
        WHEN NOT MATCHED THEN
        INSERT (PRV_Ced_Ruc, PRV_Nombre, PRV_Direccion, PRV_Telefono, PRV_Correo, CREATED_AT, UPDATED_AT)
        VALUES ('$prvId', 'Prov Test', 'Dir Test', '0999999999', 'test@prov.com', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ");

    $sub = DB::connection($prodConn)->selectOne("SELECT sct_codigo FROM subcategorias FETCH FIRST 1 ROWS ONLY");
    $sctId = $sub ? $sub->sct_codigo : null;

    // Si no hay subcategorias, simplemente pasamos NULL si la columna lo permite. 
    // Migracion dice nullable(). 

    $sql = "INSERT INTO productos (PRO_Codigo, PRO_Nombre, PRO_Descripcion, PRO_Precio, PRO_Stock, PRO_Color, PRO_Marca, ACTIVO, CREATED_AT, UPDATED_AT, PRV_Ced_Ruc, PRO_Talla";
    $vals = "VALUES ('$testCode', 'Test Producto', 'Prueba replicacion', 100.00, 10, 'Negro', 'Generica', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$prvId', '{\"talla\": \"M\"}'";

    if ($sctId) {
        $sql .= ", SCT_Codigo";
        $vals .= ", '$sctId'";
    }

    $sql .= ")";
    $vals .= ")";

    DB::connection($prodConn)->statement("$sql $vals");
    logMsg("  [OK] Producto insertado en PROD\n");

    sleep(2);

    $enComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM productos WHERE PRO_Codigo = '$testCode'")->cnt;
    if ($enComee == 1) {
        logMsg("  [OK] REPLICACION FUNCIONANDO! Producto aparecio en COMEE\n");
    } else {
        logMsg("  [FAIL] Producto NO se replico a COMEE\n");
    }

    DB::connection($prodConn)->statement("DELETE FROM productos WHERE PRO_Codigo = '$testCode'");
} catch (\Exception $e) {
    logMsg("  [ERROR] Error probando Productos: " . substr($e->getMessage(), 0, 150) . "\n");
}

logMsg("\n2. Insertando cliente en PROD (master)...\n");
$testRuc = '9' . time();
try {
    DB::connection($prodConn)->statement("
        INSERT INTO clientes (CLI_Ced_Ruc, CLI_Nombre, CLI_Telefono, CLI_Correo, CREATED_AT, UPDATED_AT)
        VALUES ('$testRuc', 'Test Cliente', '0999999999', 'test@test.com', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ");
    logMsg("  [OK] Cliente insertado en PROD\n");

    sleep(2);

    $enComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes WHERE CLI_Ced_Ruc = '$testRuc'")->cnt;
    if ($enComee == 1) {
        logMsg("  [OK] REPLICACION FUNCIONANDO! Cliente aparecio en COMEE\n");
    } else {
        logMsg("  [FAIL] Cliente NO se replico a COMEE\n");
    }

    DB::connection($prodConn)->statement("DELETE FROM clientes WHERE CLI_Ced_Ruc = '$testRuc'");
} catch (\Exception $e) {
    logMsg("  [ERROR] Error probando Clientes: " . substr($e->getMessage(), 0, 150) . "\n");
}

logMsg("\n[OK] Pruebas completadas!\n");
