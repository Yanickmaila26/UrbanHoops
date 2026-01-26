-- ============================================================================
-- PROCEDIMIENTO ALMACENADO: sp_emitir_factura
-- Descripción: Emite una factura desde una orden (versión simplificada)
-- Usa el mismo enfoque que sp_finalizar_compra_web que ya funciona
-- ============================================================================

CREATE OR REPLACE PROCEDURE sp_emitir_factura(
    p_tipo_orden IN VARCHAR2,        -- 'WEB' o 'POS'
    p_orden_id IN VARCHAR2,          -- ID de la orden (carrito o pedido)
    p_factura_id OUT VARCHAR2,
    p_success OUT NUMBER,
    p_message OUT VARCHAR2
) AS
    v_cliente_id VARCHAR2(13);
    v_count NUMBER;
    v_nuevo_factura_id VARCHAR2(20);
    v_total NUMBER(10,2) := 0;
    v_subtotal NUMBER(10,2) := 0;
    v_iva NUMBER(10,2) := 0;
    v_iva_rate NUMBER(5,2) := 0.15;
    
BEGIN
    p_success := 0;
    p_message := '';
    
    -- Validar orden según tipo
    IF p_tipo_orden = 'WEB' THEN
        -- Validar carrito
        SELECT COUNT(*) INTO v_count 
        FROM "carritos" 
        WHERE "CRC_Carrito" = p_orden_id;
        
        IF v_count = 0 THEN
            p_message := 'El carrito especificado no existe.';
            RETURN;
        END IF;
        
        SELECT "CLI_Ced_Ruc" INTO v_cliente_id
        FROM "carritos"
        WHERE "CRC_Carrito" = p_orden_id;
        
        -- Calcular total del carrito
        SELECT SUM(p."PRO_Precio" * dc."CRD_Cantidad")
        INTO v_subtotal
        FROM "detalle_carrito" dc
        JOIN "productos" p ON dc."PRO_Codigo" = p."PRO_Codigo"
        WHERE dc."CRC_Carrito" = p_orden_id;
        
    ELSIF p_tipo_orden = 'POS' THEN
        -- Validar pedido
        SELECT COUNT(*) INTO v_count 
        FROM "pedidos" 
        WHERE "PED_Codigo" = p_orden_id;
        
        IF v_count = 0 THEN
            p_message := 'El pedido especificado no existe.';
            RETURN;
        END IF;
        
        SELECT "CLI_Ced_Ruc" INTO v_cliente_id
        FROM "pedidos"
        WHERE "PED_Codigo" = p_orden_id;

        -- PED_TOTAL removed from schema. Try to get from linked Invoice if exists
        BEGIN
            SELECT f."FAC_Subtotal" INTO v_subtotal
            FROM "pedidos" p
            JOIN "facturas" f ON p."PED_FAC_Codigo" = f."FAC_Codigo"
            WHERE p."PED_Codigo" = p_orden_id;
        EXCEPTION WHEN NO_DATA_FOUND THEN
            v_subtotal := 0; -- Cannot determine total if invoice doesn't exist yet
        END;
        
    ELSE
        p_message := 'Tipo de orden inválido. Use WEB o POS.';
        RETURN;
    END IF;
    
    -- Generar siguiente ID de factura (mismo método que funciona en sp_finalizar_compra_web)
    SELECT 'FAC' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR("FAC_Codigo", 4))), 0) + 1, 5, '0')
    INTO v_nuevo_factura_id
    FROM "facturas";
    
    -- Calcular IVA y total
    v_iva := v_subtotal * v_iva_rate;
    v_total := v_subtotal + v_iva;
    
    -- Insertar factura
    INSERT INTO "facturas" (
        "FAC_Codigo",
        "CLI_Ced_Ruc",
        "FAC_Subtotal",
        "FAC_IVA",
        "FAC_Total",
        "FAC_Estado",
        "created_at",
        "updated_at"
    ) VALUES (
        v_nuevo_factura_id,
        v_cliente_id,
        v_subtotal,
        v_iva,
        v_total,
        'Pen',
        SYSDATE,
        SYSDATE
    );
    
    -- Copiar detalles según tipo de orden
    IF p_tipo_orden = 'WEB' THEN
        INSERT INTO "detalle_factura" (
            "FAC_Codigo",
            "PRO_Codigo",
            "DFC_Cantidad",
            "DFC_Precio",
            "created_at",
            "updated_at"
        )
        SELECT 
            v_nuevo_factura_id,
            dc."PRO_Codigo",
            dc."CRD_Cantidad",
            p."PRO_Precio",
            SYSDATE,
            SYSDATE
        FROM "detalle_carrito" dc
        JOIN "productos" p ON dc."PRO_Codigo" = p."PRO_Codigo"
        WHERE dc."CRC_Carrito" = p_orden_id;
        
        -- Limpiar carrito
        DELETE FROM "detalle_carrito" WHERE "CRC_Carrito" = p_orden_id;
        DELETE FROM "carritos" WHERE "CRC_Carrito" = p_orden_id;
        
    ELSIF p_tipo_orden = 'POS' THEN
        -- Marcar pedido como facturado
        UPDATE "pedidos"
        SET "PED_Estado" = 'Facturado',
            "updated_at" = SYSDATE
        WHERE "PED_Codigo" = p_orden_id;
    END IF;
    
    -- Commit de la transacción
    COMMIT;
    
    p_factura_id := v_nuevo_factura_id;
    p_success := 1;
    p_message := 'Factura emitida exitosamente: ' || v_nuevo_factura_id;
    
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        p_success := 0;
        p_message := 'Error al emitir factura: ' || SQLERRM;
END sp_emitir_factura;
/
