-- ============================================================================
-- PHASE 4: CREATE SYNONYMS IN PROD PDB
-- Description: Creates synonyms for tables that now reside in comee
--              This allows transparent access to remote tables
--              Execute this script CONNECTED TO PROD as u_prod
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT Creating Synonyms in PROD PDB
PROMPT Connected as: u_prod
PROMPT ========================================

-- First, verify database link exists
PROMPT Verifying database link: link_comee...
DECLARE
    v_count NUMBER;
    v_test VARCHAR2(10);
BEGIN
    SELECT COUNT(*) INTO v_count FROM user_db_links WHERE db_link = 'LINK_COMEE';
    IF v_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Database link LINK_COMEE does not exist!');
    END IF;
    
    -- Test connectivity
    EXECUTE IMMEDIATE 'SELECT ''OK'' FROM dual@link_comee' INTO v_test;
    DBMS_OUTPUT.PUT_LINE('✓ Database link LINK_COMEE is active and working');
END;
/

-- Create synonyms for tables that were moved to COMEE
PROMPT Creating synonyms for COMEE tables...

-- CLIENTES
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM clientes';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM clientes FOR u_comee.clientes@link_comee;
PROMPT ✓ Synonym: clientes -> u_comee.clientes@link_comee

-- DATOS_FACTURACION
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM datos_facturacion';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM datos_facturacion FOR u_comee.datos_facturacion@link_comee;
PROMPT ✓ Synonym: datos_facturacion -> u_comee.datos_facturacion@link_comee

-- CARRITOS
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM carritos';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM carritos FOR u_comee.carritos@link_comee;
PROMPT ✓ Synonym: carritos -> u_comee.carritos@link_comee

-- DETALLE_CARRITO
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM detalle_carrito';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM detalle_carrito FOR u_comee.detalle_carrito@link_comee;
PROMPT ✓ Synonym: detalle_carrito -> u_comee.detalle_carrito@link_comee

-- PEDIDOS
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM pedidos';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM pedidos FOR u_comee.pedidos@link_comee;
PROMPT ✓ Synonym: pedidos -> u_comee.pedidos@link_comee

PROMPT
PROMPT ========================================
PROMPT ✅ Phase 4 Complete: PROD Synonyms Created
PROMPT ========================================
PROMPT
PROMPT WARNING: Do NOT drop the original tables yet!
PROMPT Next Step: Create replication triggers
PROMPT ========================================

EXIT;
