-- ============================================================================
-- TRIGGER: trg_kardex_producto_bodega
-- ESTADO: DESACTIVADO
-- Razón: La lógica de inserción en Kardex se maneja desde la aplicación (PHP)
-- para garantizar que se incluyan los códigos de transacción (TRN_Codigo)
-- y factura (FAC_Codigo), que son obligatorios y que este trigger desconoce.
-- ============================================================================

-- Si el trigger existe, se debe eliminar manualmente o mediante migración.
-- DROP TRIGGER trg_kardex_producto_bodega;
