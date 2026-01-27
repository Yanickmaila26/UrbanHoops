<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- FORCING CREATION OF BODEGA TABLES IN PROD ---\n";

try {
    // 1. Create BODEGAS
    echo "Creating BODEGAS...\n";
    try {
        DB::connection('oracle')->statement("
            CREATE TABLE BODEGAS (
                BOD_Codigo VARCHAR2(15) PRIMARY KEY,
                BOD_Nombre VARCHAR2(30),
                BOD_Direccion VARCHAR2(50),
                BOD_Ciudad VARCHAR2(30),
                BOD_Pais VARCHAR2(30),
                BOD_CodigoPostal VARCHAR2(10),
                BOD_Responsable VARCHAR2(50),
                CREATED_AT TIMESTAMP,
                UPDATED_AT TIMESTAMP
            )
        ");
        echo " - BODEGAS created.\n";
    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'ORA-00955')) {
            echo " - BODEGAS already exists.\n";
        } else {
            throw $e;
        }
    }

    // 2. Create PRODUCTO_BODEGA
    echo "Creating PRODUCTO_BODEGA...\n";
    try {
        DB::connection('oracle')->statement("
            CREATE TABLE PRODUCTO_BODEGA (
                PRO_Codigo VARCHAR2(20),
                BOD_Codigo VARCHAR2(15),
                PXB_Stock NUMBER DEFAULT 0,
                CREATED_AT TIMESTAMP,
                UPDATED_AT TIMESTAMP,
                CONSTRAINT pk_prod_bod_uniq UNIQUE (PRO_Codigo, BOD_Codigo),
                CONSTRAINT fk_pb_prod FOREIGN KEY (PRO_Codigo) REFERENCES PRODUCTOS(PRO_Codigo) ON DELETE CASCADE,
                CONSTRAINT fk_pb_bod FOREIGN KEY (BOD_Codigo) REFERENCES BODEGAS(BOD_Codigo) ON DELETE CASCADE
            )
        ");
        echo " - PRODUCTO_BODEGA created.\n";
    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'ORA-00955')) {
            echo " - PRODUCTO_BODEGA already exists.\n";
        } else {
            throw $e;
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
