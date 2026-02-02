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
    
    -- Cursor de detalles con bloqueo de filas para evitar modificaciones externas
    CURSOR c_detalles IS
        SELECT pro_codigo, cantidad_solicitada
        FROM detalle_ord_com
        WHERE orc_numero = p_orden_numero
        FOR UPDATE; -- Bloquea los detalles de la orden
        
BEGIN
    p_success := 0;
    p_message := '';
    
    -- 1. VALIDACIÓN Y BLOQUEO DE LA ORDEN DE COMPRA
    -- Usamos FOR UPDATE para asegurar que nadie más procese esta orden ahora mismo
    BEGIN
        SELECT prv_ced_ruc INTO v_proveedor_id
        FROM orden_compras
        WHERE orc_numero = p_orden_numero
        FOR UPDATE;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            p_message := 'La orden de compra no existe.';
            RETURN;
    END;
    
    -- 2. VALIDAR QUE LA BODEGA EXISTE
    SELECT COUNT(*) INTO v_count
    FROM bodegas
    WHERE bod_codigo = p_bodega_codigo;
    
    IF v_count = 0 THEN
        p_message := 'La bodega especificada no existe.';
        ROLLBACK;
        RETURN;
    END IF;
    
    -- 3. PROCESAR CADA DETALLE
    FOR detalle IN c_detalles LOOP
        v_producto_codigo := detalle.pro_codigo;
        v_cantidad := detalle.cantidad_solicitada;
        
        -- A. Asegurar existencia en bodega y bloquear registro de stock
        BEGIN
            -- Bloqueamos el registro de stock específico de esta bodega
            SELECT pxb_stock INTO v_stock_actual
            FROM producto_bodega
            WHERE pro_codigo = v_producto_codigo
              AND bod_codigo = p_bodega_codigo
            FOR UPDATE;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                INSERT INTO producto_bodega (pro_codigo, bod_codigo, pxb_stock, created_at, updated_at)
                VALUES (v_producto_codigo, p_bodega_codigo, 0, SYSDATE, SYSDATE);
                v_stock_actual := 0;
        END;
        
        -- B. Actualizar stock en la bodega específica
        UPDATE producto_bodega
        SET pxb_stock = pxb_stock + v_cantidad,
            updated_at = SYSDATE
        WHERE pro_codigo = v_producto_codigo
          AND bod_codigo = p_bodega_codigo;
          
        -- C. Actualizar STOCK GLOBAL con bloqueo preventivo
        UPDATE productos
        SET pro_stock = NVL(pro_stock, 0) + v_cantidad,
            updated_at = SYSDATE
        WHERE pro_codigo = v_producto_codigo;
        
        -- D. INSERTAR EN KARDEX
        -- Obtenemos el máximo actual bloqueando la tabla de facturas/kardex si fuera necesario
        -- pero aquí usamos la lógica de incremento sobre el último ID.
        SELECT 'KDX' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR(kar_codigo, 4))), 0) + 1, 8, '0')
        INTO v_nuevo_kardex_id
        FROM kardexes;
        
        INSERT INTO kardexes (
            kar_codigo, bod_codigo, trn_codigo, orc_numero, pro_codigo, kar_cantidad, created_at, updated_at
        ) VALUES (
            v_nuevo_kardex_id, p_bodega_codigo, 'T01', p_orden_numero, v_producto_codigo, v_cantidad, SYSDATE, SYSDATE
        );
        
    END LOOP;
    
    -- 4. FINALIZAR ORDEN DE COMPRA
    UPDATE orden_compras
    SET orc_estado = 0, -- Recibido
        updated_at = SYSDATE
    WHERE orc_numero = p_orden_numero;
    
    COMMIT; -- Libera todos los bloqueos
    
    p_success := 1;
    p_message := 'Ingreso a bodega registrado exitosamente. Orden: ' || p_orden_numero;
    
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        p_success := 0;
        p_message := 'Error en sp_registrar_ingreso_bodega: ' || SQLERRM;
END sp_registrar_ingreso_bodega;
