-- ============================================================================
-- TRIGGER: trg_kardex_producto_bodega
-- Descripción: Registra automáticamente movimientos de inventario en KARDEX
-- Usa la estructura real de la tabla KARDEXES
-- ============================================================================

CREATE OR REPLACE TRIGGER trg_kardex_producto_bodega
AFTER UPDATE OF PXB_STOCK ON PRODUCTO_BODEGA
FOR EACH ROW
DECLARE
    v_nuevo_kardex_id VARCHAR2(20);
    v_cantidad_movida NUMBER;
BEGIN
    IF :NEW.PXB_STOCK != :OLD.PXB_STOCK THEN
        
        v_cantidad_movida := :NEW.PXB_STOCK - :OLD.PXB_STOCK;
        
        -- Generar nuevo ID para kardex
        SELECT 'KDX' || LPAD(NVL(MAX(TO_NUMBER(SUBSTR(KAR_CODIGO, 4))), 0) + 1, 8, '0')
        INTO v_nuevo_kardex_id
        FROM KARDEXES;
        
        -- Insertar registro en KARDEX con las columnas que realmente existen
        INSERT INTO KARDEXES (
            KAR_CODIGO,
            PRO_CODIGO,
            BOD_CODIGO,
            KAR_CANTIDAD,
            CREATED_AT,
            UPDATED_AT
        ) VALUES (
            v_nuevo_kardex_id,
            :NEW.PRO_CODIGO,
            :NEW.BOD_CODIGO,
            v_cantidad_movida,
            SYSDATE,
            SYSDATE
        );
    END IF;
END;
/
