-- ============================================================================
-- PHASE 8: DROP ORIGINAL TABLES FROM PROD (AFTER TESTING)
-- Description: Drops the original tables from prod that were moved to comee
--              ONLY execute this AFTER verifying the application works
--              Execute this script CONNECTED TO PROD as u_prod
--
-- WARNING: This is DESTRUCTIVE! Only run after backup and testing!
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT WARNING: DESTRUCTIVE OPERATION
PROMPT This will DROP tables from PROD
PROMPT ========================================
PROMPT
PROMPT Tables to be dropped:
PROMPT - pedidos
PROMPT - detalle_carrito
PROMPT - carritos
PROMPT - datos_facturacion
PROMPT - clientes
PROMPT
PROMPT These tables now reside in COMEE
PROMPT and are accessed via synonyms
PROMPT
PROMPT ⚠️  Have you:
PROMPT   1. Backed up the database?
PROMPT   2. Tested the application thoroughly?
PROMPT   3. Verified synonyms work correctly?
PROMPT
PROMPT Type 'YES' to continue or Ctrl+C to cancel:
ACCEPT v_confirm CHAR PROMPT 'Confirm: '

DECLARE
    v_confirm VARCHAR2(10) := '&v_confirm';
BEGIN
    IF UPPER(v_confirm) != 'YES' THEN
        RAISE_APPLICATION_ERROR(-20001, 'Operation cancelled by user');
    END IF;
END;
/

PROMPT
PROMPT Dropping tables from PROD...

-- Drop tables in reverse dependency order
BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE pedidos CASCADE CONSTRAINTS';
    DBMS_OUTPUT.PUT_LINE('✓ Dropped table: PEDIDOS');
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -942 THEN
            DBMS_OUTPUT.PUT_LINE('  Table PEDIDOS already dropped');
        ELSE
            RAISE;
        END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE detalle_carrito CASCADE CONSTRAINTS';
    DBMS_OUTPUT.PUT_LINE('✓ Dropped table: DETALLE_CARRITO');
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -942 THEN
            DBMS_OUTPUT.PUT_LINE('  Table DETALLE_CARRITO already dropped');
        ELSE
            RAISE;
        END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE carritos CASCADE CONSTRAINTS';
    DBMS_OUTPUT.PUT_LINE('✓ Dropped table: CARRITOS');
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -942 THEN
            DBMS_OUTPUT.PUT_LINE('  Table CARRITOS already dropped');
        ELSE
            RAISE;
        END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE datos_facturacion CASCADE CONSTRAINTS';
    DBMS_OUTPUT.PUT_LINE('✓ Dropped table: DATOS_FACTURACION');
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -942 THEN
            DBMS_OUTPUT.PUT_LINE('  Table DATOS_FACTURACION already dropped');
        ELSE
            RAISE;
        END IF;
END;
/

BEGIN
    EXECUTE IMMEDIATE 'DROP TABLE clientes CASCADE CONSTRAINTS';
    DBMS_OUTPUT.PUT_LINE('✓ Dropped table: CLIENTES');
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE = -942 THEN
            DBMS_OUTPUT.PUT_LINE('  Table CLIENTES already dropped');
        ELSE
            RAISE;
        END IF;
END;
/

PROMPT
PROMPT ========================================
PROMPT ✅ Phase 8 Complete: Tables Dropped
PROMPT ========================================
PROMPT
PROMPT The following tables have been removed from PROD:
PROMPT - clientes (now in COMEE)
PROMPT - datos_facturacion (now in COMEE)
PROMPT - carritos (now in COMEE)
PROMPT - detalle_carrito (now in COMEE)
PROMPT - pedidos (now in COMEE)
PROMPT
PROMPT Access is now via synonyms -> COMEE
PROMPT ========================================

EXIT;
