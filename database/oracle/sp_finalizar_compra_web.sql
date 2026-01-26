-- ============================================================================
-- PROCEDIMIENTO ALMACENADO: sp_finalizar_compra_web
-- Descripción: Convierte un carrito en una factura completa con transacción
-- CORREGIDO: Usa nombres de columnas exactos de la BD
-- ============================================================================

CREATE OR REPLACE PROCEDURE sp_finalizar_compra_web(
    p_carrito_id IN VARCHAR2,
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
    
    SELECT COUNT(*) INTO v_count 
    FROM "carritos" 
    WHERE "CRC_Carrito" = p_carrito_id;
    
    IF v_count = 0 THEN
        p_message := 'El carrito especificado no existe.';
        RETURN;
    END IF;
    
    SELECT "CLI_Ced_Ruc" INTO v_cliente_id
    FROM "carritos"
    WHERE "CRC_Carrito" = p_carrito_id;
    
    SELECT COUNT(*) INTO v_count
    FROM "detalle_carrito"
    WHERE "CRC_Carrito" = p_carrito_id;
    
    IF v_count = 0 THEN
        p_message := 'El carrito está vacío.';
        RETURN;
    END IF;
    
    SELECT 'FAC' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR("FAC_Codigo", 4))), 0) + 1, 5, '0')
    INTO v_nuevo_factura_id
    FROM "facturas";
    
    SELECT SUM(p."PRO_Precio" * dc."CRD_Cantidad")
    INTO v_subtotal
    FROM "detalle_carrito" dc
    JOIN "productos" p ON dc."PRO_Codigo" = p."PRO_Codigo"
    WHERE dc."CRC_Carrito" = p_carrito_id;
    
    v_iva := v_subtotal * v_iva_rate;
    v_total := v_subtotal + v_iva;
    
    -- Crear la factura (sin FAC_FECHA, usa timestamps automáticos)
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
    
    -- Mover detalles del carrito a detalle_factura
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
    WHERE dc."CRC_Carrito" = p_carrito_id;
    
    DELETE FROM "detalle_carrito"
    WHERE "CRC_Carrito" = p_carrito_id;
    
    DELETE FROM "carritos"
    WHERE "CRC_Carrito" = p_carrito_id;
    
    COMMIT;
    
    p_factura_id := v_nuevo_factura_id;
    p_success := 1;
    p_message := 'Compra finalizada exitosamente. Factura: ' || v_nuevo_factura_id;
    
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        p_success := 0;
        p_message := 'Error al procesar la compra: ' || SQLERRM;
END sp_finalizar_compra_web;
/
