create or replace PROCEDURE sp_finalizar_compra_web(
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
    
    -- Cursor para bloquear la cabecera del carrito
    CURSOR c_lock_carrito IS 
        SELECT cli_ced_ruc FROM carritos@link_comee 
        WHERE crc_carrito = p_carrito_id FOR UPDATE;

BEGIN
    p_success := 0;
    p_message := '';
    
    -- 1. BLOQUEO Y VALIDACIÓN DE EXISTENCIA
    OPEN c_lock_carrito;
    FETCH c_lock_carrito INTO v_cliente_id;
    
    IF c_lock_carrito%NOTFOUND THEN
        CLOSE c_lock_carrito;
        p_message := 'El carrito especificado no existe.';
        RETURN;
    END IF;
    CLOSE c_lock_carrito;
    
    -- 2. BLOQUEO DE LÍNEAS DE DETALLE (Evita ORA-01786)
    DECLARE
        CURSOR c_lock_detalle IS 
            SELECT * FROM detalle_carrito@link_comee 
            WHERE crc_carrito = p_carrito_id FOR UPDATE;
    BEGIN
        OPEN c_lock_detalle;
        CLOSE c_lock_detalle;
    END;

    -- 3. VALIDACIÓN DE CARRITO VACÍO Y CÁLCULO DE SUBTOTAL
    SELECT NVL(SUM(p.pro_precio * dc.crd_cantidad), 0)
    INTO v_subtotal
    FROM detalle_carrito@link_comee dc
    JOIN productos p ON dc.pro_codigo = p.pro_codigo
    WHERE dc.crc_carrito = p_carrito_id;
    
    IF v_subtotal <= 0 THEN
        p_message := 'El carrito está vacío o no tiene productos válidos.';
        ROLLBACK;
        RETURN;
    END IF;
    
    -- 4. GENERAR ID DE FACTURA
    SELECT 'FAC' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR(fac_codigo, 4))), 0) + 1, 5, '0')
    INTO v_nuevo_factura_id
    FROM facturas;
    
    v_iva := v_subtotal * v_iva_rate;
    v_total := v_subtotal + v_iva;
    
    -- 5. CREAR LA FACTURA
    INSERT INTO facturas (
        fac_codigo,
        cli_ced_ruc,
        fac_subtotal,
        fac_iva,
        fac_total,
        fac_estado,
        created_at,
        updated_at
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
    
    -- 6. MOVER DETALLES A DETALLE_FACTURA
    INSERT INTO detalle_factura@link_comee (
        fac_codigo,
        pro_codigo,
        dfc_cantidad,
        dfc_precio,
        created_at,
        updated_at
    )
    SELECT 
        v_nuevo_factura_id,
        dc.pro_codigo,
        dc.crd_cantidad,
        p.pro_precio,
        SYSDATE,
        SYSDATE
    FROM detalle_carrito dc
    JOIN productos p ON dc.pro_codigo = p.pro_codigo
    WHERE dc.crc_carrito = p_carrito_id;
    
    -- 7. LIMPIEZA DE TABLAS TEMPORALES
    DELETE FROM detalle_carrito@link_comee WHERE crc_carrito = p_carrito_id;
    DELETE FROM carritos@link_comee WHERE crc_carrito = p_carrito_id;
    
    -- COMMIT LIBERA TODOS LOS BLOQUEOS
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