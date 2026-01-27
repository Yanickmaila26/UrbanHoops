-- ============================================================================
-- PHASE 3: CREATE SYNONYMS IN COMEE PDB
-- Description: Creates synonyms for tables that reside in prod
--              This allows transparent access to remote tables
--              Execute this script CONNECTED TO COMEE as u_comee
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT Creating Synonyms in COMEE PDB
PROMPT Connected as: u_comee
PROMPT ========================================

-- First, verify database link exists
PROMPT Verifying database link: link_prod...
DECLARE
    v_count NUMBER;
    v_test VARCHAR2(10);
BEGIN
    SELECT COUNT(*) INTO v_count FROM user_db_links WHERE db_link = 'LINK_PROD';
    IF v_count = 0 THEN
        RAISE_APPLICATION_ERROR(-20001, 'Database link LINK_PROD does not exist!');
    END IF;
    
    -- Test connectivity
    EXECUTE IMMEDIATE 'SELECT ''OK'' FROM dual@link_prod' INTO v_test;
    DBMS_OUTPUT.PUT_LINE('✓ Database link LINK_PROD is active and working');
END;
/

-- Create synonyms for tables that remain in PROD
PROMPT Creating synonyms for PROD tables...

-- PROVEEDORS - suppliers table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM proveedors';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM proveedors FOR u_prod.proveedors@link_prod;
PROMPT ✓ Synonym: proveedors -> u_prod.proveedors@link_prod

-- BODEGAS - warehouse table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM bodegas';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM bodegas FOR u_prod.bodegas@link_prod;
PROMPT ✓ Synonym: bodegas -> u_prod.bodegas@link_prod

-- KARDEXES - inventory movements table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM kardexes';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM kardexes FOR u_prod.kardexes@link_prod;
PROMPT ✓ Synonym: kardexes -> u_prod.kardexes@link_prod

-- PRODUCTO_BODEGA - product warehouse relationship
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM producto_bodega';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM producto_bodega FOR u_prod.producto_bodega@link_prod;
PROMPT ✓ Synonym: producto_bodega -> u_prod.producto_bodega@link_prod

-- ORDEN_COMPRAS - purchase orders
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM orden_compras';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM orden_compras FOR u_prod.orden_compras@link_prod;
PROMPT ✓ Synonym: orden_compras -> u_prod.orden_compras@link_prod

-- TRANSACCIONS - transactions table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM transaccions';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM transaccions FOR u_prod.transaccions@link_prod;
PROMPT ✓ Synonym: transaccions -> u_prod.transaccions@link_prod

-- CATEGORIAS - categories table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM categorias';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM categorias FOR u_prod.categorias@link_prod;
PROMPT ✓ Synonym: categorias -> u_prod.categorias@link_prod

-- SUBCATEGORIAS - subcategories table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM subcategorias';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM subcategorias FOR u_prod.subcategorias@link_prod;
PROMPT ✓ Synonym: subcategorias -> u_prod.subcategorias@link_prod

-- USUARIO_APLICACIONS - application users table
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM usuario_aplicacions';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM usuario_aplicacions FOR u_prod.usuario_aplicacions@link_prod;
PROMPT ✓ Synonym: usuario_aplicacions -> u_prod.usuario_aplicacions@link_prod

-- USERS - Laravel auth users
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM users';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM users FOR u_prod.users@link_prod;
PROMPT ✓ Synonym: users -> u_prod.users@link_prod

-- CACHE, JOBS, and other Laravel tables
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM cache';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM cache FOR u_prod.cache@link_prod;
PROMPT ✓ Synonym: cache -> u_prod.cache@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM cache_locks';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM cache_locks FOR u_prod.cache_locks@link_prod;
PROMPT ✓ Synonym: cache_locks -> u_prod.cache_locks@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM jobs';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM jobs FOR u_prod.jobs@link_prod;
PROMPT ✓ Synonym: jobs -> u_prod.jobs@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM job_batches';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM job_batches FOR u_prod.job_batches@link_prod;
PROMPT ✓ Synonym: job_batches -> u_prod.job_batches@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM failed_jobs';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM failed_jobs FOR u_prod.failed_jobs@link_prod;
PROMPT ✓ Synonym: failed_jobs -> u_prod.failed_jobs@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM sessions';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM sessions FOR u_prod.sessions@link_prod;
PROMPT ✓ Synonym: sessions -> u_prod.sessions@link_prod

-- Permission tables (Spatie)
BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM permissions';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM permissions FOR u_prod.permissions@link_prod;
PROMPT ✓ Synonym: permissions -> u_prod.permissions@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM roles';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM roles FOR u_prod.roles@link_prod;
PROMPT ✓ Synonym: roles -> u_prod.roles@link_prod

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM model_has_permissions';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM model_has_permissions FOR u_prod.model_has_permissions@link_prod;
PROMPT ✓ Synonym: model_has_permissions

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM model_has_roles';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM model_has_roles FOR u_prod.model_has_roles@link_prod;
PROMPT ✓ Synonym: model_has_roles

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM role_has_permissions';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM role_has_permissions FOR u_prod.role_has_permissions@link_prod;
PROMPT ✓ Synonym: role_has_permissions

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM personal_access_tokens';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM personal_access_tokens FOR u_prod.personal_access_tokens@link_prod;
PROMPT ✓ Synonym: personal_access_tokens

BEGIN
    EXECUTE IMMEDIATE 'DROP SYNONYM migrations';
EXCEPTION
    WHEN OTHERS THEN
        IF SQLCODE != -1434 THEN RAISE; END IF;
END;
/

CREATE SYNONYM migrations FOR u_prod.migrations@link_prod;
PROMPT ✓ Synonym: migrations

PROMPT
PROMPT ========================================
PROMPT ✅ Phase 3 Complete: Synonyms Created
PROMPT ========================================
PROMPT
PROMPT Next Step:
PROMPT Execute scripts in PROD database
PROMPT ========================================

EXIT;
