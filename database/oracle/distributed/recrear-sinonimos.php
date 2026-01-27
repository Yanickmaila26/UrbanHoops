<?php

/**
 * Script para recrear sinónimos en PROD correctamente
 */

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Recreando Sinónimos en PROD                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';

$tablasEnComee = [
    'CLIENTES',
    'DATOS_FACTURACION',
    'CARRITOS',
    'DETALLE_CARRITO',
    'PEDIDOS'
];

echo "Verificando que el database link funciona...\n";
try {
    DB::connection($prodConn)->select("SELECT 1 FROM dual@link_comee");
    echo "  ✓ Database link PROD->COMEE funcionando\n\n";
} catch (\Exception $e) {
    echo "  ✗ Database link no funciona: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Eliminando sinónimos existentes (si existen)...\n";
foreach ($tablasEnComee as $tabla) {
    try {
        DB::connection($prodConn)->statement("DROP SYNONYM $tabla");
        echo "  ✓ Sinónimo $tabla eliminado\n";
    } catch (\Exception $e) {
        // No existe, continuar
    }
}

echo "\n";
echo "Creando sinónimos nuevos...\n";
foreach ($tablasEnComee as $tabla) {
    try {
        $sql = "CREATE SYNONYM $tabla FOR u_comee.$tabla@link_comee";
        DB::connection($prodConn)->statement($sql);
        echo "  ✓ Sinónimo $tabla creado -> u_comee.$tabla@link_comee\n";
    } catch (\Exception $e) {
        echo "  ✗ Error creando sinónimo $tabla: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "Verificando acceso vía sinónimos...\n";

foreach ($tablasEnComee as $tabla) {
    try {
        $count = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM $tabla");
        echo "  ✓ $tabla: {$count->cnt} registros\n";
    } catch (\Exception $e) {
        echo "  ✗ Error accediendo a $tabla: " . substr($e->getMessage(), 0, 80) . "...\n";
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Probando INSERT en cliente...\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    $testRuc = '9999999999990';

    // Eliminar si existe
    DB::connection($prodConn)->statement("DELETE FROM clientes WHERE CLI_Ced_Ruc = '$testRuc'");

    // Insertar
    echo "Insertando cliente de prueba en PROD (debería ir a COMEE vía sinónimo)...\n";
    DB::connection($prodConn)->statement("
        INSERT INTO clientes (CLI_Ced_Ruc, CLI_Nombre, CLI_Telefono, CLI_Correo)
        VALUES ('$testRuc', 'Test Cliente', '0999999999', 'test@test.com')
    ");

    echo "  ✓ INSERT ejecutado en PROD\n";

    // Verificar en PROD (vía sinónimo)
    $enProd = DB::connection($prodConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes WHERE CLI_Ced_Ruc = '$testRuc'");
    echo "  ✓ Verificación en PROD (vía sinónimo): {$enProd->cnt} registro\n";

    // Verificar directamente en COMEE
    $enComee = DB::connection($comeeConn)->selectOne("SELECT COUNT(*) as cnt FROM clientes WHERE CLI_Ced_Ruc = '$testRuc'");
    echo "  ✓ Verificación en COMEE (directo): {$enComee->cnt} registro\n";

    if ($enProd->cnt == 1 && $enComee->cnt == 1) {
        echo "\n✅ Los sinónimos están funcionando correctamente!\n";
        echo "El INSERT desde PROD fue a COMEE vía sinónimo.\n";
    }

    // Limpiar
    DB::connection($prodConn)->statement("DELETE FROM clientes WHERE CLI_Ced_Ruc = '$testRuc'");
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Sinónimos recreados                                          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "Ahora deberías poder usar normalmente:\n";
echo "  php artisan tinker\n";
echo "  >>> App\\Models\\Cliente::count()\n\n";
