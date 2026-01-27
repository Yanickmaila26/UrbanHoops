-- ============================================================================
-- VERIFICATION QUERIES FOR DISTRIBUTED DATABASE
-- Description: Test queries to verify the distributed database setup
--              Run these queries after completing all phases
-- ============================================================================

SET SERVEROUTPUT ON;
SET LINESIZE 200;

PROMPT ========================================
PROMPT Distributed Database Verification
PROMPT ========================================

-- ============================================================================
-- Test 1: Database Link Connectivity
-- ============================================================================
PROMPT
PROMPT TEST 1: Database Link Connectivity
PROMPT -----------------------------------

-- Test from PROD to COMEE
SELECT 'PROD->COMEE: ' || dummy AS test_result FROM dual@link_comee;

-- Test from COMEE to PROD (run this in COMEE)
-- SELECT 'COMEE->PROD: ' || dummy AS test_result FROM dual@link_prod;

-- ============================================================================
-- Test 2: Synonym Resolution
-- ============================================================================
PROMPT
PROMPT TEST 2: Synonym Resolution (from PROD)
PROMPT ---------------------------------------

-- These should access COMEE via synonyms
SELECT 'clientes' AS table_name, COUNT(*) AS row_count FROM clientes
UNION ALL
SELECT 'carritos', COUNT(*) FROM carritos
UNION ALL
SELECT 'detalle_carrito', COUNT(*) FROM detalle_carrito
UNION ALL
SELECT 'pedidos', COUNT(*) FROM pedidos
UNION ALL
SELECT 'datos_facturacion', COUNT(*) FROM datos_facturacion;

-- ============================================================================
-- Test 3: Replica Table Synchronization
-- ============================================================================
PROMPT
PROMPT TEST 3: Replica Table Row Count Comparison
PROMPT -------------------------------------------

SELECT 
    'productos' AS table_name,
    (SELECT COUNT(*) FROM productos) AS prod_count,
    (SELECT COUNT(*) FROM productos@link_comee) AS comee_count,
    CASE 
        WHEN (SELECT COUNT(*) FROM productos) = (SELECT COUNT(*) FROM productos@link_comee)
        THEN '✓ MATCH'
        ELSE '✗ MISMATCH'
    END AS status
FROM dual
UNION ALL
SELECT 
    'facturas',
    (SELECT COUNT(*) FROM facturas),
    (SELECT COUNT(*) FROM facturas@link_comee),
    CASE 
        WHEN (SELECT COUNT(*) FROM facturas) = (SELECT COUNT(*) FROM facturas@link_comee)
        THEN '✓ MATCH'
        ELSE '✗ MISMATCH'
    END
FROM dual;

-- ============================================================================
-- Test 4 INSERT Trigger Test (Productos)
-- ============================================================================
PROMPT
PROMPT TEST 4: Testing Productos INSERT Trigger
PROMPT -----------------------------------------

-- Insert a test product in PROD
INSERT INTO productos (
    PRO_Codigo, PRV_Ced_Ruc, PRO_Nombre, PRO_Descripcion,
    PRO_Color, PRO_Talla, PRO_Marca, PRO_Precio, PRO_Stock,
    ACTIVO, CREATED_AT, UPDATED_AT
) VALUES (
    'TEST_PROD_001', '1234567890', 'Test Product', 'This is a test product',
    'Red', 'M', 'TestBrand', 99.99, 10,
    1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
);

COMMIT;

-- Verify it exists in COMEE
PROMPT Checking if test product replicated to COMEE...
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ Product replicated successfully to COMEE'
        ELSE '✗ Product NOT found in COMEE (trigger may have failed)'
    END AS replication_status
FROM productos@link_comee
WHERE PRO_Codigo = 'TEST_PROD_001';

-- ============================================================================
-- Test 5: UPDATE Trigger Test (Productos)
-- ============================================================================
PROMPT
PROMPT TEST 5: Testing Productos UPDATE Trigger
PROMPT -----------------------------------------

-- Update the test product in PROD
UPDATE productos 
SET PRO_Precio = 149.99,
    PRO_Stock = 20,
    UPDATED_AT = CURRENT_TIMESTAMP
WHERE PRO_Codigo = 'TEST_PROD_001';

COMMIT;

-- Verify update replicated to COMEE
PROMPT Checking if update replicated to COMEE...
SELECT 
    PRO_Codigo,
    PRO_Precio,
    PRO_Stock,
    CASE 
        WHEN PRO_Precio = 149.99 AND PRO_Stock = 20 
        THEN '✓ Update replicated'
        ELSE '✗ Update NOT replicated'
    END AS replication_status
FROM productos@link_comee
WHERE PRO_Codigo = 'TEST_PROD_001';

-- ============================================================================
-- Test 6: Foreign Key Integrity Across Databases
-- ============================================================================
PROMPT
PROMPT TEST 6: Foreign Key Integrity Test
PROMPT -----------------------------------

-- Try to insert a cart detail for non-existent cliente (should fail)
PROMPT Testing referential integrity (this should FAIL)...

DECLARE
    v_error_msg VARCHAR2(4000);
BEGIN
    INSERT INTO detalle_carrito (CRC_Carrito, PRO_Codigo, CRD_Cantidad)
    VALUES ('INVALID_CART', 'TEST_PROD_001', 1);
    
    DBMS_OUTPUT.PUT_LINE('✗ ERROR: Insert should have failed but succeeded!');
    ROLLBACK;
EXCEPTION
    WHEN OTHERS THEN
        v_error_msg := SQLERRM;
        IF SQLCODE = -2291 THEN -- Parent key not found
            DBMS_OUTPUT.PUT_LINE('✓ Foreign key constraint working correctly');
            DBMS_OUTPUT.PUT_LINE('  Error: ' || v_error_msg);
        ELSE
            DBMS_OUTPUT.PUT_LINE('✗ Unexpected error: ' || v_error_msg);
        END IF;
        ROLLBACK;
END;
/

-- ============================================================================
-- Test 7: Clean Up Test Data
-- ============================================================================
PROMPT
PROMPT TEST 7: Cleaning Up Test Data
PROMPT ------------------------------

-- Delete test product
DELETE FROM productos WHERE PRO_Codigo = 'TEST_PROD_001';
COMMIT;

-- Verify deletion replicated
PROMPT Checking if DELETE replicated to COMEE...
SELECT 
    CASE 
        WHEN COUNT(*) = 0 THEN '✓ Delete replicated successfully to COMEE'
        ELSE '✗ Product still exists in COMEE'
    END AS replication_status
FROM productos@link_comee
WHERE PRO_Codigo = 'TEST_PROD_001';

-- ============================================================================
-- Test 8: Performance Check
-- ============================================================================
PROMPT
PROMPT TEST 8: Query Performance Check
PROMPT --------------------------------

SET TIMING ON;

-- Query joining local and remote tables
SELECT 
    c.CLI_Nombre,
    COUNT(p.PED_Codigo) AS total_pedidos,
    SUM(p.PED_Total) AS total_gastado
FROM clientes c
LEFT JOIN pedidos p ON c.CLI_Ced_Ruc = p.PED_CLI_Codigo
GROUP BY c.CLI_Nombre
FETCH FIRST 10 ROWS ONLY;

SET TIMING OFF;

PROMPT
PROMPT ========================================
PROMPT ✅ Verification Complete
PROMPT ========================================
PROMPT
PROMPT Review the results above. All tests
PROMPT should show '✓' for success.
PROMPT ========================================
