-- ============================================================================
-- PROCEDIMIENTO ALMACENADO: sp_registrar_ingreso_bodega
-- Descripción: Registra ingreso de productos a bodega desde una orden de compra
-- Nota: Usa los nombres de columnas correctos según las migraciones
-- ============================================================================

CREATE OR REPLACE PROCEDURE sp_registrar_ingreso_bodega(
    p_orden_numero IN VARCHAR2,
    p_bodega_codigo IN VARCHAR2,
    p_success OUT NUMBER,
    p_message OUT VARCHAR2
) AS
    v_count NUMBER;
    v_proveedor_id VARCHAR2(13);
    v_producto_codigo VARCHAR2(20);
    v_cantidad NUMBER;
    v_stock_actual NUMBER;
    
    CURSOR c_detalles IS
        SELECT PRO_CODIGO, cantidad_solicitada
        FROM DETALLE_ORD_COM
        WHERE ORC_NUMERO = p_orden_numero;
        
BEGIN
    p_success := 0;
    p_message := '';
    
    -- Validar que la orden de compra existe
    SELECT COUNT(*) INTO v_count
    FROM ORDEN_COMPRAS
    WHERE ORC_NUMERO = p_orden_numero;
    
    IF v_count = 0 THEN
        p_message := 'La orden de compra no existe.';
        RETURN;
    END IF;
    
    -- Validar que la bodega existe
    SELECT COUNT(*) INTO v_count
    FROM BODEGAS
    WHERE BOD_CODIGO = p_bodega_codigo;
    
    IF v_count = 0 THEN
        p_message := 'La bodega especificada no existe.';
        RETURN;
    END IF;
    
    -- Obtener proveedor de la orden
    SELECT PRV_CED_RUC INTO v_proveedor_id
    FROM ORDEN_COMPRAS
    WHERE ORC_NUMERO = p_orden_numero;
    
    -- Procesar cada detalle de la orden de compra
    FOR detalle IN c_detalles LOOP
        v_producto_codigo := detalle.PRO_CODIGO;
        v_cantidad := detalle.CANTIDAD_SOLICITADA;
        
        -- Obtener stock actual en la bodega
        BEGIN
            SELECT PXB_STOCK INTO v_stock_actual
            FROM PRODUCTO_BODEGA
            WHERE PRO_CODIGO = v_producto_codigo
              AND BOD_CODIGO = p_bodega_codigo;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                -- Si no existe registro, crear uno con stock 0
                INSERT INTO PRODUCTO_BODEGA (PRO_CODIGO, BOD_CODIGO, PXB_STOCK, CREATED_AT, UPDATED_AT)
                VALUES (v_producto_codigo, p_bodega_codigo, 0, SYSDATE, SYSDATE);
                v_stock_actual := 0;
        END;
        
        -- Actualizar stock sumando la cantidad ingresada
        UPDATE PRODUCTO_BODEGA
        SET PXB_STOCK = PXB_STOCK + v_cantidad,
            UPDATED_AT = SYSDATE
        WHERE PRO_CODIGO = v_producto_codigo
          AND BOD_CODIGO = p_bodega_codigo;
        
        -- NOTA: El trigger trg_kardex_producto_bodega registrará automáticamente
        -- el movimiento en la tabla KARDEX después de este UPDATE
        
    END LOOP;
    
    -- Actualizar estado de la orden de compra (cambiar a FALSE = recibida)
    UPDATE ORDEN_COMPRAS
    SET ORC_ESTADO = 0,
        UPDATED_AT = SYSDATE
    WHERE ORC_NUMERO = p_orden_numero;
    
    -- Confirmar transacción
    COMMIT;
    
    p_success := 1;
    p_message := 'Ingreso a bodega registrado exitosamente. Orden: ' || p_orden_numero;
    
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        p_success := 0;
        p_message := 'Error al registrar ingreso: ' || SQLERRM;
END sp_registrar_ingreso_bodega;
/
