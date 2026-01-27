-- ============================================================================
-- Script 02: Crear tablas REPLICADAS en COMEE - COLUMNAS CORRECTAS
-- Ejecutar en: COMEE como u_comee
-- ============================================================================

SET SERVEROUTPUT ON;
PROMPT ════════════════════════════════════════════════════════════════
PROMPT Creando tablas REPLICADAS en COMEE
PROMPT (Según migraciones de Laravel)
PROMPT ════════════════════════════════════════════════════════════════

-- CLIENTES (Master en COMEE)
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE CLIENTES CASCADE CONSTRAINTS';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -942 THEN RAISE; END IF;
END;
/

CREATE TABLE CLIENTES (
    CLI_Ced_Ruc VARCHAR2(13) PRIMARY KEY,
    CLI_Nombre VARCHAR2(60) NOT NULL,
    CLI_Telefono VARCHAR2(10),
    CLI_Correo VARCHAR2(60) UNIQUE,
    usuario_aplicacion_id NUMBER,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP
);

PROMPT ✓ Tabla CLIENTES creada (MASTER)

-- FACTURAS (Master en COMEE)
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE FACTURAS CASCADE CONSTRAINTS';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -942 THEN RAISE; END IF;
END;
/

CREATE TABLE FACTURAS (
    FAC_Codigo VARCHAR2(15) PRIMARY KEY,
    CLI_Ced_Ruc VARCHAR2(13) NOT NULL,
    FAC_IVA NUMBER NOT NULL,
    FAC_Subtotal NUMBER(10,2) NOT NULL,
    FAC_Total NUMBER(10,2) NOT NULL,
    FAC_Estado VARCHAR2(3) DEFAULT 'Pen' CHECK (FAC_Estado IN ('Pen', 'Pag', 'Anu')),
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP,
    CONSTRAINT fk_facturas_cliente FOREIGN KEY (CLI_Ced_Ruc) 
        REFERENCES CLIENTES(CLI_Ced_Ruc)
);

PROMPT ✓ Tabla FACTURAS creada (MASTER)

-- PROVEEDORS (Sinónimo)
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM PROVEEDORS';
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/
CREATE SYNONYM PROVEEDORS FOR u_prod.PROVEEDORS@link_prod;
PROMPT ✓ Sinónimo PROVEEDORS creado

-- SUBCATEGORIAS (Sinónimo)
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM SUBCATEGORIAS';
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/
CREATE SYNONYM SUBCATEGORIAS FOR u_prod.SUBCATEGORIAS@link_prod;
PROMPT ✓ Sinónimo SUBCATEGORIAS creado

-- PRODUCTOS (Réplica en COMEE, master en PROD)
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE PRODUCTOS CASCADE CONSTRAINTS';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -942 THEN RAISE; END IF;
END;
/

CREATE TABLE PRODUCTOS (
    PRO_Codigo VARCHAR2(15) PRIMARY KEY,
    PRV_Ced_Ruc VARCHAR2(13),
    SCT_Codigo VARCHAR2(10),
    PRO_Nombre VARCHAR2(60) NOT NULL,
    PRO_Descripcion VARCHAR2(255),
    PRO_Color VARCHAR2(15),
    PRO_Talla VARCHAR2(5),
    PRO_Marca VARCHAR2(20),
    PRO_Precio NUMBER(10,2) NOT NULL,
    PRO_Stock NUMBER DEFAULT 0,
    PRO_Imagen VARCHAR2(255),
    ACTIVO NUMBER(1) DEFAULT 1,
    CREATED_AT TIMESTAMP,
    UPDATED_AT TIMESTAMP
);

PROMPT ✓ Tabla PRODUCTOS creada (RÉPLICA)

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT Tablas REPLICADAS creadas exitosamente en COMEE
PROMPT ════════════════════════════════════════════════════════════════

EXIT;
