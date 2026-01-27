<?php

/**
 * Script simplificado para setup de base de datos distribuida - VERSIÓN CORRECTA
 * PROD es MASTER para Clientes, Facturas, Productos
 */

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║   Setup Distribuido - PROD MASTER                             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$prodConn = 'oracle';
$comeeConn = 'oracle_comee';


// echo "¿Continuar con setup? (escribe 'SI'): ";
// $handle = fopen("php://stdin", "r");
// $line = fgets($handle);
// fclose($handle);

// if (trim($line) !== 'SI') {
//    echo "\nOperación cancelada.\n";
//    exit(0);
// }


echo "\n═══════════════════════════════════════════════════════════════\n";
echo "Paso 1: Ejecutando migraciones en PROD (Master)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

passthru('php artisan migrate:fresh-oracle --force', $return);

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "Paso 2: Creando tablas REPLICADAS en COMEE (Destino)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// CLIENTES en COMEE (Réplica)
echo "Creando CLIENTES en COMEE (Réplica)...\n";
try {
    DB::connection($comeeConn)->statement("DROP TABLE CLIENTES CASCADE CONSTRAINTS");
} catch (\Exception $e) {
}

DB::connection($comeeConn)->statement("
CREATE TABLE CLIENTES (
    CLI_Ced_Ruc VARCHAR2(13) PRIMARY KEY,
    CLI_Nombre VARCHAR2(60) NOT NULL,
    CLI_Telefono VARCHAR2(10),
    CLI_Correo VARCHAR2(60) UNIQUE,
    usuario_aplicacion_id NUMBER,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP
)
");
echo "  ✓ Creada\n\n";

// FACTURAS en COMEE (Réplica)
echo "Creando FACTURAS en COMEE (Réplica)...\n";
try {
    DB::connection($comeeConn)->statement("DROP TABLE FACTURAS CASCADE CONSTRAINTS");
} catch (\Exception $e) {
}

DB::connection($comeeConn)->statement("
CREATE TABLE FACTURAS (
    FAC_Codigo VARCHAR2(15) PRIMARY KEY,
    CLI_Ced_Ruc VARCHAR2(13) NOT NULL,
    FAC_IVA NUMBER NOT NULL,
    FAC_Subtotal NUMBER(10,2) NOT NULL,
    FAC_Total NUMBER(10,2) NOT NULL,
    FAC_Estado VARCHAR2(3) DEFAULT 'Pen' CHECK (FAC_Estado IN ('Pen', 'Pag', 'Anu')),
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_facturas_cliente FOREIGN KEY (CLI_Ced_Ruc) REFERENCES CLIENTES(CLI_Ced_Ruc)
)
");
echo "  ✓ Creada\n\n";

// PRODUCTOS en COMEE (Réplica)
echo "Creando PRODUCTOS en COMEE (Réplica)...\n";

try {
    DB::connection($comeeConn)->statement("DROP SYNONYM PROVEEDORS");
} catch (\Exception $e) {
}
try {
    DB::connection($comeeConn)->statement("DROP SYNONYM SUBCATEGORIAS");
} catch (\Exception $e) {
}

DB::connection($comeeConn)->statement("CREATE SYNONYM PROVEEDORS FOR u_prod.PROVEEDORS@link_prod");
DB::connection($comeeConn)->statement("CREATE SYNONYM SUBCATEGORIAS FOR u_prod.SUBCATEGORIAS@link_prod");

try {
    DB::connection($comeeConn)->statement("DROP TABLE PRODUCTOS CASCADE CONSTRAINTS");
} catch (\Exception $e) {
}

DB::connection($comeeConn)->statement("
CREATE TABLE PRODUCTOS (
    PRO_Codigo VARCHAR2(15) PRIMARY KEY,
    PRV_Ced_Ruc VARCHAR2(13),
    SCT_Codigo VARCHAR2(10),
    PRO_Nombre VARCHAR2(60) NOT NULL,
    PRO_Descripcion VARCHAR2(255),
    PRO_Color VARCHAR2(15),
    PRO_Talla CLOB,
    PRO_Marca VARCHAR2(20),
    PRO_Precio NUMBER(10,2) NOT NULL,
    PRO_Stock NUMBER DEFAULT 0,
    PRO_Imagen VARCHAR2(255),
    ACTIVO NUMBER(1) DEFAULT 1,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP
)
");
echo "  ✓ Creada\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "Paso 3: Creando Tablas DISTRIBUIDAS en COMEE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Ejecutar script 01 para tablas distribuidas
passthru('php database/oracle/distributed/crear-tablas-distribuidas.php', $return);

echo "═══════════════════════════════════════════════════════════════\n";
echo "Paso 4: Instalando triggers en PROD\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

passthru('php database/oracle/distributed/instalar-triggers.php', $return);

echo "\n✅ Setup completado! PROD es Master para todo.\n\n";
echo "Ahora ejecuta: php artisan db:seed\n";
