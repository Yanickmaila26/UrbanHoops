-- ============================================================================
-- PHASE 7: MIGRATE EXISTING DATA FROM PROD TO COMEE
-- Description: Migrates existing data from prod to comee
--              Must be executed AFTER all tables and triggers are created
--              Execute this script CONNECTED TO PROD as u_prod
--
-- IMPORTANT: This will DISABLE triggers during migration to avoid loops
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT Data Migration: PROD -> COMEE
PROMPT Connected as: u_prod
PROMPT ========================================

-- ============================================================================
-- Step 1: Disable replication triggers to avoid circular replication
-- ============================================================================
PROMPT Step 1: Disabling replication triggers...

ALTER TRIGGER trg_productos_insert_repl DISABLE;
ALTER TRIGGER trg_productos_update_repl DISABLE;
ALTER TRIGGER trg_productos_delete_repl DISABLE;
ALTER TRIGGER trg_facturas_insert_repl DISABLE;
ALTER TRIGGER trg_detalle_factura_insert_repl DISABLE;

PROMPT ✓ Triggers disabled in PROD

-- Connect to COMEE and disable its triggers too (needs to be done via SQL*Plus separately)
PROMPT 
PROMPT ⚠️  MANUAL STEP REQUIRED:
PROMPT Connect to COMEE and run:
PROMPT     ALTER TRIGGER trg_productos_insert_repl DISABLE;
PROMPT     ALTER TRIGGER trg_productos_update_repl DISABLE;
PROMPT     ALTER TRIGGER trg_productos_delete_repl DISABLE;
PROMPT     ALTER TRIGGER trg_facturas_insert_repl DISABLE;
PROMPT     ALTER TRIGGER trg_detalle_factura_insert_repl DISABLE;
PROMPT
PROMPT Press ENTER when done...
PAUSE

-- ============================================================================
-- Step 2: Migrate customer-related data to COMEE
-- ============================================================================
PROMPT Step 2: Migrating customer data...

DECLARE
    v_count NUMBER;
BEGIN
    -- Migrate CLIENTES
    INSERT INTO clientes@link_comee 
    SELECT * FROM clientes;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Migrated ' || v_count || ' rows to CLIENTES@link_comee');
    
    -- Migrate DATOS_FACTURACION
    INSERT INTO datos_facturacion@link_comee
    SELECT * FROM datos_facturacion;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Migrated ' || v_count || ' rows to DATOS_FACTURACION@link_comee');
    
    -- Migrate CARRITOS
    INSERT INTO carritos@link_comee
    SELECT * FROM carritos;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Migrated ' || v_count || ' rows to CARRITOS@link_comee');
    
    -- Migrate DETALLE_CARRITO
    INSERT INTO detalle_carrito@link_comee
    SELECT * FROM detalle_carrito;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Migrated ' || v_count || ' rows to DETALLE_CARRITO@link_comee');
    
    -- Migrate PEDIDOS
    INSERT INTO pedidos@link_comee
    SELECT * FROM pedidos;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Migrated ' || v_count || ' rows to PEDIDOS@link_comee');
    
    COMMIT;
END;
/

-- ============================================================================
-- Step 3: Replicate productos and facturas to COMEE
-- ============================================================================
PROMPT Step 3: Replicating productos and facturas...

DECLARE
    v_count NUMBER;
BEGIN
    -- Replicate PRODUCTOS
    INSERT INTO productos@link_comee
    SELECT * FROM productos;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Replicated ' || v_count || ' rows to PRODUCTOS@link_comee');
    
    -- Replicate FACTURAS
    INSERT INTO facturas@link_comee
    SELECT * FROM facturas;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Replicated ' || v_count || ' rows to FACTURAS@link_comee');
    
    -- Replicate DETALLE_FACTURA
    INSERT INTO detalle_factura@link_comee
    SELECT * FROM detalle_factura;
    v_count := SQL%ROWCOUNT;
    DBMS_OUTPUT.PUT_LINE('✓ Replicated ' || v_count || ' rows to DETALLE_FACTURA@link_comee');
    
    COMMIT;
END;
/

-- ============================================================================
-- Step 4: Verify row counts match
-- ============================================================================
PROMPT Step 4: Verifying data migration...

DECLARE
    v_prod_count NUMBER;
    v_comee_count NUMBER;
    v_table_name VARCHAR2(50);
    
    PROCEDURE verify_table(p_table_name VARCHAR2) IS
    BEGIN
        EXECUTE IMMEDIATE 'SELECT COUNT(*) FROM ' || p_table_name INTO v_prod_count;
        EXECUTE IMMEDIATE 'SELECT COUNT(*) FROM ' || p_table_name || '@link_comee' INTO v_comee_count;
        
        IF v_prod_count = v_comee_count THEN
            DBMS_OUTPUT.PUT_LINE('✓ ' || p_table_name || ': ' || v_prod_count || ' rows (match)');
        ELSE
            DBMS_OUTPUT.PUT_LINE('✗ ' || p_table_name || ': PROD=' || v_prod_count || 
                               ' COMEE=' || v_comee_count || ' (MISMATCH!)');
        END IF;
    END;
BEGIN
    DBMS_OUTPUT.PUT_LINE('Verification Results:');
    DBMS_OUTPUT.PUT_LINE('--------------------');
    verify_table('clientes');
    verify_table('datos_facturacion');
    verify_table('carritos');
    verify_table('detalle_carrito');
    verify_table('pedidos');
    verify_table('productos');
    verify_table('facturas');
    verify_table('detalle_factura');
END;
/

-- ============================================================================
-- Step 5: Re-enable replication triggers
-- ============================================================================
PROMPT Step 5: Re-enabling replication triggers...

ALTER TRIGGER trg_productos_insert_repl ENABLE;
ALTER TRIGGER trg_productos_update_repl ENABLE;
ALTER TRIGGER trg_productos_delete_repl ENABLE;
ALTER TRIGGER trg_facturas_insert_repl ENABLE;
ALTER TRIGGER trg_detalle_factura_insert_repl ENABLE;

PROMPT ✓ Triggers enabled in PROD

PROMPT 
PROMPT ⚠️  MANUAL STEP REQUIRED:
PROMPT Connect to COMEE and run:
PROMPT     ALTER TRIGGER trg_productos_insert_repl ENABLE;
PROMPT     ALTER TRIGGER trg_productos_update_repl ENABLE;
PROMPT     ALTER TRIGGER trg_productos_delete_repl ENABLE;
PROMPT     ALTER TRIGGER trg_facturas_insert_repl ENABLE;
PROMPT     ALTER TRIGGER trg_detalle_factura_insert_repl ENABLE;
PROMPT

PROMPT
PROMPT ========================================
PROMPT ✅ Phase 7 Complete: Data Migrated
PROMPT ========================================
PROMPT
PROMPT WARNING: Do NOT drop original tables yet!
PROMPT Test the application first with synonyms
PROMPT Next Step: Test application functionality
PROMPT ========================================

EXIT;
