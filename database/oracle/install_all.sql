-- ============================================================================
-- SCRIPT DE INSTALACIÓN COMPLETA - ORACLE URBANHOOPS
-- Ejecuta todos los procedimientos y triggers en orden
-- ============================================================================

PROMPT ========================================
PROMPT Instalando Objetos Oracle - UrbanHoops
PROMPT ========================================
PROMPT

-- 1. Procedimiento: Finalizar Compra Web
PROMPT [1/4] Instalando sp_finalizar_compra_web...
@@sp_finalizar_compra_web.sql
PROMPT ... OK

-- 2. Trigger: Auditoría de Clientes
PROMPT [2/4] Instalando trg_auditoria_clientes...
@@trg_auditoria_clientes.sql
PROMPT ... OK

-- 3. Procedimiento: Registrar Ingreso a Bodega
PROMPT [3/4] Instalando sp_registrar_ingreso_bodega...
@@sp_registrar_ingreso_bodega.sql
PROMPT ... OK

-- 4. Triggers: Movimiento de Kardex
PROMPT [4/4] Instalando triggers de kardex...
@@trg_movimiento_kardex.sql
PROMPT ... OK

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
    'TRG_AUDITORIA_CLIENTES',
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
