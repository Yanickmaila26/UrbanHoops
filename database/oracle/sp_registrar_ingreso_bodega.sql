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
    v_nuevo_kardex_id VARCHAR2(20);
    
    CURSOR c_detalles IS
        SELECT "PRO_Codigo", "cantidad_solicitada"
        FROM "detalle_ord_com"
        WHERE "ORC_Numero" = p_orden_numero;
        
BEGIN
    p_success := 0;
    p_message := '';
    
    -- Validar que la orden de compra existe
    SELECT COUNT(*) INTO v_count
    FROM "orden_compras"
    WHERE "ORC_Numero" = p_orden_numero;
    
    IF v_count = 0 THEN
        p_message := 'La orden de compra no existe.';
        RETURN;
    END IF;
    
    -- Validar que la bodega existe
    SELECT COUNT(*) INTO v_count
    FROM "bodegas"
    WHERE "BOD_Codigo" = p_bodega_codigo;
    
    IF v_count = 0 THEN
        p_message := 'La bodega especificada no existe.';
        RETURN;
    END IF;
    
    -- Obtener proveedor de la orden
    SELECT "PRV_Ced_Ruc" INTO v_proveedor_id
    FROM "orden_compras"
    WHERE "ORC_Numero" = p_orden_numero;
    
    -- Procesar cada detalle de la orden de compra
    FOR detalle IN c_detalles LOOP
        v_producto_codigo := detalle."PRO_Codigo";
        v_cantidad := detalle."cantidad_solicitada";
        
        -- Obtener stock actual en la bodega
        BEGIN
            SELECT "PXB_Stock" INTO v_stock_actual
            FROM "producto_bodega"
            WHERE "PRO_Codigo" = v_producto_codigo
              AND "BOD_Codigo" = p_bodega_codigo;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                -- Si no existe registro, crear uno con stock 0
                INSERT INTO "producto_bodega" ("PRO_Codigo", "BOD_Codigo", "PXB_Stock", "created_at", "updated_at")
                VALUES (v_producto_codigo, p_bodega_codigo, 0, SYSDATE, SYSDATE);
                v_stock_actual := 0;
        END;
        
        -- Actualizar stock sumando la cantidad ingresada
        UPDATE "producto_bodega"
        SET "PXB_Stock" = "PXB_Stock" + v_cantidad,
            "updated_at" = SYSDATE
        WHERE "PRO_Codigo" = v_producto_codigo
          AND "BOD_Codigo" = p_bodega_codigo;
          
        -- Actualizar también el STOCK GLOBAL del PRODUCTO (requisito del usuario)
        UPDATE "productos"
        SET "PRO_Stock" = NVL("PRO_Stock", 0) + v_cantidad,
            "updated_at" = SYSDATE
        WHERE "PRO_Codigo" = v_producto_codigo;
        
        -- INSERTAR EN KARDEX (Reemplaza al trigger)
        -- Generar ID
        SELECT 'KDX' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR("KAR_Codigo", 4))), 0) + 1, 8, '0')
        INTO v_nuevo_kardex_id
        FROM "kardexes";
        
        INSERT INTO "kardexes" (
            "KAR_Codigo",
            "BOD_Codigo",
            "TRN_Codigo",
            "ORC_Numero",
            "FAC_Codigo",
            "PRO_Codigo",
            "KAR_cantidad",
            "created_at",
            "updated_at"
        ) VALUES (
            v_nuevo_kardex_id,
            p_bodega_codigo,
            'T01',           -- T01: Compra de Mercadería (Hardcoded para este SP de Ingreso)
            p_orden_numero,  -- Vinculamos la compra
            NULL,
            v_producto_codigo,
            v_cantidad,
            SYSDATE,
            SYSDATE
        );
        
    END LOOP;
    
    -- Actualizar estado de la orden de compra (cambiar a FALSE = recibida, o status int)
    UPDATE "orden_compras"
    SET "ORC_Estado" = 0, -- Asumiendo 0 = Recibido
        "updated_at" = SYSDATE
    WHERE "ORC_Numero" = p_orden_numero;
    
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
