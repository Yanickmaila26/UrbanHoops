-- ============================================================================
-- TRIGGER: trg_bloqueo_facturas
-- Descripción: Protege integridad fiscal impidiendo modificar facturas
-- Se ejecuta BEFORE DELETE o BEFORE UPDATE
-- ============================================================================

CREATE OR REPLACE TRIGGER trg_bloqueo_facturas
BEFORE DELETE OR UPDATE ON "facturas"
FOR EACH ROW
BEGIN
    -- Impedir eliminación de facturas (integridad fiscal)
    IF DELETING THEN
        RAISE_APPLICATION_ERROR(
            -20001,
            'ERROR FISCAL: No se pueden eliminar facturas emitidas. ' ||
            'La factura ' || :OLD."FAC_Codigo" || ' está protegida por integridad fiscal.'
        );
    END IF;
    
    -- Impedir modificación de campos críticos de facturas
    IF UPDATING THEN
        -- Permitir solo actualización de estado de pago
        IF :OLD."FAC_Codigo" != :NEW."FAC_Codigo" THEN
            RAISE_APPLICATION_ERROR(
                -20002,
                'ERROR FISCAL: No se puede modificar el código de factura. ' ||
                'Factura ' || :OLD."FAC_Codigo" || ' protegida.'
            );
        END IF;
        
        IF :OLD."CLI_Ced_Ruc" != :NEW."CLI_Ced_Ruc" THEN
            RAISE_APPLICATION_ERROR(
                -20003,
                'ERROR FISCAL: No se puede modificar el cliente de una factura. ' ||
                'Factura ' || :OLD."FAC_Codigo" || ' protegida.'
            );
        END IF;
        
        IF :OLD."FAC_Subtotal" != :NEW."FAC_Subtotal" OR
           :OLD."FAC_IVA" != :NEW."FAC_IVA" OR
           :OLD."FAC_Total" != :NEW."FAC_Total" THEN
            RAISE_APPLICATION_ERROR(
                -20004,
                'ERROR FISCAL: No se pueden modificar los montos de una factura. ' ||
                'Factura ' || :OLD."FAC_Codigo" || ' protegida.'
            );
        END IF;
        
        -- Solo se permite cambiar el estado entre: Pen (Pendiente), Pag (Pagada), Anu (Anulada)
        -- Validación adicional: No se puede cambiar de Anu a otro estado
        IF :OLD."FAC_Estado" = 'Anu' AND :NEW."FAC_Estado" != 'Anu' THEN
            RAISE_APPLICATION_ERROR(
                -20005,
                'ERROR FISCAL: No se puede reactivar una factura anulada. ' ||
                'Factura ' || :OLD."FAC_Codigo" || ' está anulada permanentemente.'
            );
        END IF;
    END IF;
END;
/
