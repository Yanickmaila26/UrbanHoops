<?php

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "═══════════════════════════════════════════════════════════════\n";
echo "CREANDO TABLAS RÉPLICA EN PROD\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$prodConn = 'oracle';

// CLIENTES (réplica en PROD)
echo "Creando CLIENTES en PROD...\n";
try {
    DB::connection($prodConn)->statement("DROP TABLE CLIENTES CASCADE CONSTRAINTS");
} catch (\Exception $e) {
}

DB::connection($prodConn)->statement("
CREATE TABLE CLIENTES (
    CLI_Ced_Ruc VARCHAR2(13) PRIMARY KEY,
    CLI_Nombre VARCHAR2(100) NOT NULL,
    CLI_Telefono VARCHAR2(15),
    CLI_Correo VARCHAR2(100) UNIQUE,
    CLI_Direccion VARCHAR2(255),
    CLI_FechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CLI_Estado CHAR(1) DEFAULT 'A' CHECK (CLI_Estado IN ('A', 'I'))
)
");
echo "  ✓ CLIENTES creada en PROD\n\n";

// FACTURAS (réplica en PROD)
echo "Creando FACTURAS en PROD...\n";
try {
    DB::connection($prodConn)->statement("DROP TABLE FACTURAS CASCADE CONSTRAINTS");
} catch (\Exception $e) {
}

DB::connection($prodConn)->statement("
CREATE TABLE FACTURAS (
    FAC_Numero VARCHAR2(20) PRIMARY KEY,
    CLI_Ced_Ruc VARCHAR2(13) NOT NULL,
    FAC_Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FAC_Subtotal NUMBER(10,2) NOT NULL,
    FAC_Impuesto NUMBER(10,2) DEFAULT 0,
    FAC_Total NUMBER(10,2) NOT NULL,
    FAC_Estado VARCHAR2(20) DEFAULT 'Emitida',
    FAC_FormaPago VARCHAR2(50),
    CONSTRAINT fk_facturas_cliente_repl FOREIGN KEY (CLI_Ced_Ruc) 
        REFERENCES CLIENTES(CLI_Ced_Ruc)
)
");
echo "  ✓ FACTURAS creada en PROD\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "VERIFICANDO TABLAS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$tables = DB::connection($prodConn)->select("
    SELECT table_name 
    FROM user_tables 
    WHERE table_name IN ('CLIENTES', 'FACTURAS', 'PRODUCTOS', 'CARRITOS')
    ORDER BY table_name
");

echo "Tablas en PROD:\n";
foreach ($tables as $t) {
    echo "  ✓ " . $t->table_name . "\n";
}

echo "\n✅ Réplicas creadas en PROD\n";
