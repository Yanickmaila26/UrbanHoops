-- ============================================================================
-- SCRIPTS DE PRUEBA - ORACLE URBANHOOPS
-- Prueba todos los procedimientos almacenados y triggers
-- ============================================================================

SET SERVEROUTPUT ON SIZE 1000000
SET LINESIZE 200

PROMPT ========================================
PROMPT PRUEBAS DE PROCEDIMIENTOS Y TRIGGERS
PROMPT ========================================
PROMPT

-- ============================================================================
-- PRUEBA 1: Trigger de Auditoría de Clientes
-- ============================================================================
PROMPT
PROMPT [TEST 1] Probando trigger de auditoría...
PROMPT

-- Actualizar un cliente para disparar el trigger
DECLARE
    v_cedula VARCHAR2(13);
BEGIN
    -- Obtener un cliente existente
    SELECT CLI_CED_RUC INTO v_cedula 
    FROM CLIENTES 
    WHERE ROWNUM = 1;
    
    DBMS_OUTPUT.PUT_LINE('Cliente encontrado: ' || v_cedula);
    
    -- Actualizar correo para disparar el trigger
    UPDATE CLIENTES 
    SET CLI_CORREO = 'nuevo_' || SYSTIMESTAMP || '@test.com'
    WHERE CLI_CED_RUC = v_cedula;
    
    COMMIT;
    
    DBMS_OUTPUT.PUT_LINE('✓ Cliente actualizado - Trigger ejecutado');
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('✗ No hay clientes en la base de datos');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('✗ Error: ' || SQLERRM);
        ROLLBACK;
END;
/

-- Verificar que se creó el registro de auditoría
PROMPT Verificando log de auditoría...
SELECT 
    LOG_CAMPO,
    LOG_VALOR_ANTERIOR,
    LOG_VALOR_NUEVO,
    TO_CHAR(LOG_FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA,
    LOG_USUARIO
FROM LOG_CLIENTES
WHERE ROWNUM <= 3
ORDER BY LOG_FECHA DESC;

-- ============================================================================
-- PRUEBA 2: Procedimiento sp_finalizar_compra_web
-- ============================================================================
PROMPT
PROMPT [TEST 2] Probando sp_finalizar_compra_web...
PROMPT

DECLARE
    v_carrito_id VARCHAR2(20);
    v_cliente_id VARCHAR2(13);
    v_producto_id VARCHAR2(20);
    v_factura_id VARCHAR2(20);
    v_success NUMBER;
    v_message VARCHAR2(500);
BEGIN
    -- Obtener un cliente
    SELECT CLI_CED_RUC INTO v_cliente_id 
    FROM CLIENTES 
    WHERE ROWNUM = 1;
    
    -- Obtener un producto
    SELECT PRO_CODIGO INTO v_producto_id 
    FROM PRODUCTOS 
    WHERE ROWNUM = 1;
    
    -- Crear carrito de prueba
    v_carrito_id := 'CRCTEST' || TO_CHAR(SYSTIMESTAMP, 'FFSSS');
    
    INSERT INTO CARRITOS (CRC_CARRITO, CLI_CED_RUC, CREATED_AT, UPDATED_AT)
    VALUES (v_carrito_id, v_cliente_id, SYSDATE, SYSDATE);
    
    -- Agregar producto al carrito
    INSERT INTO DETALLE_CARRITO (CRC_CARRITO, PRO_CODIGO, CRD_CANTIDAD, CREATED_AT, UPDATED_AT)
    VALUES (v_carrito_id, v_producto_id, 2, SYSDATE, SYSDATE);
    
    COMMIT;
    
    DBMS_OUTPUT.PUT_LINE('Carrito creado: ' || v_carrito_id);
    DBMS_OUTPUT.PUT_LINE('Cliente: ' || v_cliente_id);
    DBMS_OUTPUT.PUT_LINE('Producto: ' || v_producto_id);
    
    -- Llamar al procedimiento
    sp_finalizar_compra_web(v_carrito_id, v_factura_id, v_success, v_message);
    
    IF v_success = 1 THEN
        DBMS_OUTPUT.PUT_LINE('✓ ' || v_message);
        DBMS_OUTPUT.PUT_LINE('  Factura creada: ' || v_factura_id);
    ELSE
        DBMS_OUTPUT.PUT_LINE('✗ ' || v_message);
    END IF;
    
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('✗ No hay datos suficientes en la BD');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('✗ Error: ' || SQLERRM);
        ROLLBACK;
END;
/

-- Verificar factura creada
PROMPT Últimas facturas creadas:
SELECT 
    FAC_CODIGO,
    CLI_CED_RUC,
    TO_CHAR(FAC_FECHA, 'DD/MM/YYYY') AS FECHA,
    FAC_SUBTOTAL,
    FAC_IVA,
    FAC_TOTAL
FROM FACTURAS
ORDER BY FAC_FECHA DESC
FETCH FIRST 3 ROWS ONLY;

-- ============================================================================
-- PRUEBA 3: Procedimiento sp_registrar_ingreso_bodega
-- ============================================================================
PROMPT
PROMPT [TEST 3] Probando sp_registrar_ingreso_bodega...
PROMPT

DECLARE
    v_orden_numero VARCHAR2(20);
    v_proveedor_id VARCHAR2(13);
    v_producto_id VARCHAR2(20);
    v_bodega_codigo VARCHAR2(20);
    v_stock_antes NUMBER;
    v_stock_despues NUMBER;
    v_success NUMBER;
    v_message VARCHAR2(500);
BEGIN
    -- Obtener proveedor, producto y bodega
    SELECT PRV_CED_RUC INTO v_proveedor_id FROM PROVEEDORS WHERE ROWNUM = 1;
    SELECT PRO_CODIGO INTO v_producto_id FROM PRODUCTOS WHERE ROWNUM = 1;
    SELECT BOD_CODIGO INTO v_bodega_codigo FROM BODEGAS WHERE ROWNUM = 1;
    
    -- Crear orden de compra de prueba
    v_orden_numero := 'ORCTEST' || TO_CHAR(SYSTIMESTAMP, 'FFSSS');
    
    INSERT INTO ORDEN_COMPRAS (ORC_NUMERO, PRV_CED_RUC, ORC_FECHA_EMISION, ORC_FECHA_ENTREGA, ORC_MONTO_TOTAL, ORC_ESTADO, CREATED_AT, UPDATED_AT)
    VALUES (v_orden_numero, v_proveedor_id, SYSDATE, SYSDATE+7, 1000, 1, SYSDATE, SYSDATE);
    
    -- Agregar detalle
    INSERT INTO DETALLE_ORD_COM (ORC_NUMERO, PRO_CODIGO, CANTIDAD_SOLICITADA)
    VALUES (v_orden_numero, v_producto_id, 10);
    
    COMMIT;
    
    -- Obtener stock antes
    BEGIN
        SELECT PXB_STOCK INTO v_stock_antes
        FROM PRODUCTO_BODEGA
        WHERE PRO_CODIGO = v_producto_id AND BOD_CODIGO = v_bodega_codigo;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            v_stock_antes := 0;
    END;
    
    DBMS_OUTPUT.PUT_LINE('Orden creada: ' || v_orden_numero);
    DBMS_OUTPUT.PUT_LINE('Stock antes: ' || v_stock_antes);
    
    -- Llamar al procedimiento
    sp_registrar_ingreso_bodega(v_orden_numero, v_bodega_codigo, v_success, v_message);
    
    IF v_success = 1 THEN
        DBMS_OUTPUT.PUT_LINE('✓ ' || v_message);
        
        -- Obtener stock después
        SELECT PXB_STOCK INTO v_stock_despues
        FROM PRODUCTO_BODEGA
        WHERE PRO_CODIGO = v_producto_id AND BOD_CODIGO = v_bodega_codigo;
        
        DBMS_OUTPUT.PUT_LINE('  Stock después: ' || v_stock_despues);
        DBMS_OUTPUT.PUT_LINE('  Diferencia: +' || (v_stock_despues - v_stock_antes));
    ELSE
        DBMS_OUTPUT.PUT_LINE('✗ ' || v_message);
    END IF;
    
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.PUT_LINE('✗ No hay datos suficientes en la BD');
    WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('✗ Error: ' || SQLERRM);
        ROLLBACK;
END;
/

-- ============================================================================
-- PRUEBA 4: Verificar Kardex
-- ============================================================================
PROMPT
PROMPT [TEST 4] Verificando registros de Kardex...
PROMPT

SELECT 
    k.KAR_CODIGO,
    p.PRO_NOMBRE,
    b.BOD_NOMBRE,
    k.KAR_TIPO_MOVIMIENTO,
    k.KAR_CANTIDAD_ENTRADA,
    k.KAR_CANTIDAD_SALIDA,
    k.KAR_SALDO_ACTUAL,
    TO_CHAR(k.KAR_FECHA, 'DD/MM HH24:MI:SS') AS FECHA
FROM KARDEXES k
JOIN PRODUCTOS p ON k.PRO_CODIGO = p.PRO_CODIGO
JOIN BODEGAS b ON k.BOD_CODIGO = b.BOD_CODIGO
ORDER BY k.KAR_FECHA DESC
FETCH FIRST 5 ROWS ONLY;

PROMPT
PROMPT ========================================
PROMPT PRUEBAS COMPLETADAS
PROMPT ========================================
PROMPT
PROMPT Si todos los tests muestran ✓, los procedimientos funcionan correctamente.
PROMPT Revisa los resultados arriba para verificar.
PROMPT
