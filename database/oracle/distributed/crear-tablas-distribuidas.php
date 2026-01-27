<?php
// Helper para crear tablas distribuidas en COMEE que no existen en PROD
// (Carritos, Pedidos, etc)

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$comeeConn = 'oracle_comee';

function execSql($conn, $sql)
{
    try {
        DB::connection($conn)->statement($sql);
        return true;
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'ORA-00955') !== false) return true; // already exists
        echo "  ⚠ " . $e->getMessage() . "\n";
        return false;
    }
}

echo "Creando tablas distribuidas en COMEE...\n";

// DATOS FACTURACION
execSql($comeeConn, "DROP TABLE DATOS_FACTURACION CASCADE CONSTRAINTS");
execSql($comeeConn, "
CREATE TABLE DATOS_FACTURACION (
    DAF_Codigo VARCHAR2(15) PRIMARY KEY,
    DAF_CLI_Codigo VARCHAR2(13) NOT NULL,
    DAF_Direccion VARCHAR2(255),
    DAF_Ciudad VARCHAR2(255),
    DAF_Estado VARCHAR2(255),
    DAF_CP VARCHAR2(10),
    DAF_Tarjeta_Numero CLOB,
    DAF_Tarjeta_Expiracion VARCHAR2(5),
    DAF_Tarjeta_CVV CLOB,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_datos_fac_cliente FOREIGN KEY (DAF_CLI_Codigo) 
        REFERENCES CLIENTES(CLI_Ced_Ruc) ON DELETE CASCADE
)");
echo "  ✓ DATOS_FACTURACION\n";

// CARRITOS
execSql($comeeConn, "DROP TABLE CARRITOS CASCADE CONSTRAINTS");
execSql($comeeConn, "
CREATE TABLE CARRITOS (
    CRC_Carrito VARCHAR2(13) PRIMARY KEY,
    CLI_Ced_Ruc VARCHAR2(13),
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_carritos_cliente FOREIGN KEY (CLI_Ced_Ruc) 
        REFERENCES CLIENTES(CLI_Ced_Ruc) ON DELETE CASCADE
)");
echo "  ✓ CARRITOS\n";

// DETALLE_CARRITO
execSql($comeeConn, "DROP TABLE DETALLE_CARRITO CASCADE CONSTRAINTS");
execSql($comeeConn, "
CREATE TABLE DETALLE_CARRITO (
    CRC_Carrito VARCHAR2(13) NOT NULL,
    PRO_Codigo VARCHAR2(15) NOT NULL,
    CRD_Cantidad NUMBER NOT NULL,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_detalle_carrito FOREIGN KEY (CRC_Carrito) 
        REFERENCES CARRITOS(CRC_Carrito) ON DELETE CASCADE,
    CONSTRAINT fk_detalle_car_producto FOREIGN KEY (PRO_Codigo) 
        REFERENCES PRODUCTOS(PRO_Codigo) ON DELETE CASCADE
)");
echo "  ✓ DETALLE_CARRITO\n";

// DETALLE_FACTURA
execSql($comeeConn, "DROP TABLE DETALLE_FACTURA CASCADE CONSTRAINTS");
execSql($comeeConn, "
CREATE TABLE DETALLE_FACTURA (
    FAC_Codigo VARCHAR2(15) NOT NULL,
    PRO_Codigo VARCHAR2(15) NOT NULL,
    DFC_Cantidad NUMBER NOT NULL,
    DFC_Precio NUMBER(10,2) NOT NULL,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_detalle_factura FOREIGN KEY (FAC_Codigo) 
        REFERENCES FACTURAS(FAC_Codigo) ON DELETE CASCADE,
    CONSTRAINT fk_detalle_fac_producto FOREIGN KEY (PRO_Codigo) 
        REFERENCES PRODUCTOS(PRO_Codigo) ON DELETE CASCADE
)");
echo "  ✓ DETALLE_FACTURA\n";

// PEDIDOS
execSql($comeeConn, "DROP TABLE PEDIDOS CASCADE CONSTRAINTS");
execSql($comeeConn, "
CREATE TABLE PEDIDOS (
    PED_Codigo VARCHAR2(15) PRIMARY KEY,
    PED_CLI_Codigo VARCHAR2(13) NOT NULL,
    PED_DAF_Codigo VARCHAR2(15) NOT NULL,
    PED_FAC_Codigo VARCHAR2(15),
    PED_Fecha TIMESTAMP NOT NULL,
    PED_Estado VARCHAR2(20) DEFAULT 'Pendiente' CHECK (PED_Estado IN ('Pendiente', 'Procesando', 'Enviado', 'Entregado')),
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_pedidos_cliente FOREIGN KEY (PED_CLI_Codigo) 
        REFERENCES CLIENTES(CLI_Ced_Ruc) ON DELETE CASCADE,
    CONSTRAINT fk_pedidos_datos_fac FOREIGN KEY (PED_DAF_Codigo) 
        REFERENCES DATOS_FACTURACION(DAF_Codigo) ON DELETE CASCADE,
    CONSTRAINT fk_pedidos_factura FOREIGN KEY (PED_FAC_Codigo) 
        REFERENCES FACTURAS(FAC_Codigo) ON DELETE CASCADE
)");
echo "  ✓ PEDIDOS\n"; // Fixed PED_FAC_Codigo column name if it was wrong before
