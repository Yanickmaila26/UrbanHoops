CREATE OR REPLACE PROCEDURE sp_emitir_factura(
    p_tipo_orden IN VARCHAR2,        -- Solo aceptará 'WEB'
    p_orden_id IN VARCHAR2,          -- ID del carrito
    p_factura_id OUT VARCHAR2,
    p_success OUT NUMBER,
    p_message OUT VARCHAR2
) AS
    v_cliente_id VARCHAR2(13);
    v_nuevo_factura_id VARCHAR2(20);
    v_total NUMBER(10,2) := 0;
    v_subtotal NUMBER(10,2) := 0;
    v_iva NUMBER(10,2) := 0;
    v_iva_rate NUMBER(5,2) := 0.15;
    
    -- Cursor para bloquear la cabecera del carrito remoto
    CURSOR c_lock_carrito IS 
        SELECT cli_ced_ruc FROM carritos@link_comee 
        WHERE crc_carrito = p_orden_id FOR UPDATE;

BEGIN
    p_success := 0;
    p_message := '';
    
    -- 1. VALIDACIÓN DE TIPO DE ORDEN
    IF p_tipo_orden != 'WEB' THEN
        p_message := 'Error: Este procedimiento solo procesa órdenes de tipo WEB.';
        RETURN;
    END IF;

    -- 2. BLOQUEO Y OBTENCIÓN DE DATOS DEL CARRITO (REMOTO)
    OPEN c_lock_carrito;
    FETCH c_lock_carrito INTO v_cliente_id;
    IF c_lock_carrito%NOTFOUND THEN
        CLOSE c_lock_carrito;
        p_message := 'El carrito remoto especificado no existe.';
        RETURN;
    END IF;
    CLOSE c_lock_carrito;

    -- 3. BLOQUEO DE LÍNEAS DE DETALLE (REMOTO)
    -- Esto previene el error ORA-01786 al separar el bloqueo del cálculo
    DECLARE
        CURSOR c_det_c IS 
            SELECT * FROM detalle_carrito@link_comee 
            WHERE crc_carrito = p_orden_id FOR UPDATE;
    BEGIN
        OPEN c_det_c; 
        CLOSE c_det_c; 
    END;

    -- 4. CÁLCULO DE SUBTOTAL
    -- Join entre detalle_carrito (remoto) y productos (local)
    SELECT NVL(SUM(p.pro_precio * dc.crd_cantidad), 0)
    INTO v_subtotal
    FROM detalle_carrito@link_comee dc
    JOIN productos p ON dc.pro_codigo = p.pro_codigo
    WHERE dc.crc_carrito = p_orden_id;

    IF v_subtotal <= 0 THEN
        p_message := 'El carrito no tiene productos o el valor es 0.';
        ROLLBACK;
        RETURN;
    END IF;
    
    -- 5. GENERAR ID DE FACTURA LOCAL
    SELECT 'FAC' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR(fac_codigo, 4))), 0) + 1, 5, '0')
    INTO v_nuevo_factura_id
    FROM facturas;
    
    v_iva := v_subtotal * v_iva_rate;
    v_total := v_subtotal + v_iva;
    
    -- 6. INSERCIÓN DE FACTURA Y DETALLES (TABLAS LOCALES)
    INSERT INTO facturas (
        fac_codigo, cli_ced_ruc, fac_subtotal, fac_iva, fac_total, 
        fac_estado, created_at, updated_at
    ) VALUES (
        v_nuevo_factura_id, v_cliente_id, v_subtotal, v_iva, v_total, 
        'Pag', SYSDATE, SYSDATE
    );
    
    INSERT INTO detalle_factura (
        fac_codigo, pro_codigo, dfc_cantidad, dfc_precio, created_at, updated_at
    )
    SELECT 
        v_nuevo_factura_id, 
        dc.pro_codigo, 
        dc.crd_cantidad, 
        p.pro_precio, 
        SYSDATE, 
        SYSDATE
    FROM detalle_carrito@link_comee dc 
    JOIN productos p ON dc.pro_codigo = p.pro_codigo
    WHERE dc.crc_carrito = p_orden_id;
    
    -- 7. LIMPIEZA EN LA BASE DE DATOS REMOTA
    DELETE FROM detalle_carrito@link_comee WHERE crc_carrito = p_orden_id;
    DELETE FROM carritos@link_comee WHERE crc_carrito = p_orden_id;
    
    -- COMMIT FINAL (Transacción Distribuida)
    COMMIT;
    
    p_factura_id := v_nuevo_factura_id;
    p_success := 1;
    p_message := 'Factura WEB emitida exitosamente: ' || v_nuevo_factura_id;
    
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        p_success := 0;
        p_message := 'Error en sp_emitir_factura: ' || SQLERRM;
END sp_emitir_factura;
