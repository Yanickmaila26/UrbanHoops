-- ============================================================================
-- Script 02B: Crear tablas RÉPLICA en PROD - COLUMNAS CORRECTAS
-- Ejecutar en: PROD como u_prod
-- Estas son las réplicas de tablas cuyo master está en COMEE
-- ============================================================================

SET SERVEROUTPUT ON;
PROMPT ════════════════════════════════════════════════════════════════
PROMPT Creando tablas RÉPLICA en PROD
PROMPT (Réplicas sincronizadas desde COMEE)
PROMPT ════════════════════════════════════════════════════════════════

-- CLIENTES (Réplica en PROD, master en COMEE)
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

PROMPT ✓ Tabla CLIENTES creada (RÉPLICA en PROD)

-- FACTURAS (Réplica en PROD, master en COMEE)
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
    CONSTRAINT fk_facturas_cliente_repl FOREIGN KEY (CLI_Ced_Ruc) 
        REFERENCES CLIENTES(CLI_Ced_Ruc)
);

PROMPT ✓ Tabla FACTURAS creada (RÉPLICA en PROD)

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT Tablas RÉPLICA creadas en PROD
PROMPT ════════════════════════════════════════════════════════════════
PROMPT
PROMPT Las operaciones en COMEE se replicarán aquí vía triggers
PROMPT

EXIT;
