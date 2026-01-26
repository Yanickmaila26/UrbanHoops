-- ============================================================================
-- SCRIPT DE INSTALACIÓN COMPLETA - ORACLE URBANHOOPS
-- Ejecuta todos los procedimientos y triggers en orden
-- ============================================================================

PROMPT ========================================
PROMPT Instalando Objetos Oracle - UrbanHoops
PROMPT ========================================
PROMPT

-- 1. Procedimientos
PROMPT [1/6] Instalando sp_finalizar_compra_web...
@@sp_finalizar_compra_web.sql
PROMPT [2/6] Instalando sp_registrar_ingreso_bodega...
@@sp_registrar_ingreso_bodega.sql
PROMPT [3/6] Instalando sp_emitir_factura...
@@sp_emitir_factura.sql

-- 2. Triggers
PROMPT [4/6] Instalando trg_auditoria_clientes...
@@trg_auditoria_clientes.sql
PROMPT [5/6] Instalando trg_bloqueo_facturas...
@@trg_bloqueo_facturas.sql
PROMPT [6/6] Instalando triggers de kardex...
@@trg_movimiento_kardex.sql

PROMPT
PROMPT ========================================
PROMPT Verificando instalación...
PROMPT ========================================
PROMPT

-- Verificar que todos los objetos se crearon correctamente
SELECT 
    object_name AS "Objeto",
    object_type AS "Tipo",
    status AS "Estado"
FROM user_objects 
WHERE object_name IN (
    'SP_FINALIZAR_COMPRA_WEB',
    'SP_REGISTRAR_INGRESO_BODEGA',
    'SP_EMITIR_FACTURA',
    'TRG_AUDITORIA_CLIENTES',
    'TRG_BLOQUEO_FACTURAS',
    'TRG_KARDEX_PRODUCTO_BODEGA',
    'TRG_KARDEX_VENTA',
    'LOG_CLIENTES'
)
ORDER BY object_type, object_name;

PROMPT
PROMPT ========================================
PROMPT Instalación completada!
PROMPT Todos los objetos deben mostrar STATUS = VALID
PROMPT ========================================
