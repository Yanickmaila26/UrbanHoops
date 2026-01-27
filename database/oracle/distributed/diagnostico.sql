-- ============================================================================
-- Script para diagnosticar el estado actual de la distribución
-- Ejecutar en PROD como u_prod
-- ============================================================================

SET LINESIZE 200;
SET PAGESIZE 100;

PROMPT ════════════════════════════════════════════════════════════════
PROMPT Diagnóstico de Tablas en PROD
PROMPT ════════════════════════════════════════════════════════════════
PROMPT

PROMPT Tablas físicas en PROD:
PROMPT

SELECT table_name, 
       num_rows,
       CASE 
           WHEN table_name IN ('CLIENTES', 'CARRITOS', 'DETALLE_CARRITO', 'PEDIDOS', 'DATOS_FACTURACION') 
           THEN '⚠ DEBERÍA SER SINÓNIMO'
           ELSE '✓ OK en PROD'
       END as status
FROM user_tables
WHERE table_name IN (
    'CLIENTES', 'CARRITOS', 'DETALLE_CARRITO', 'PEDIDOS', 'DATOS_FACTURACION',
    'PRODUCTOS', 'FACTURAS', 'DETALLE_FACTURA'
)
ORDER BY table_name;

PROMPT
PROMPT Sinónimos en PROD:
PROMPT

SELECT synonym_name, table_owner, table_name, db_link
FROM user_synonyms
WHERE synonym_name IN (
    'CLIENTES', 'CARRITOS', 'DETALLE_CARRITO', 'PEDIDOS', 'DATOS_FACTURACION'
)
ORDER BY synonym_name;

PROMPT
PROMPT Triggers de replicación en PROD:
PROMPT

SELECT trigger_name, status, table_name
FROM user_triggers
WHERE trigger_name LIKE '%REPL%'
ORDER BY trigger_name;

EXIT;
