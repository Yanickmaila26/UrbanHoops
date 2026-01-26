-- ============================================================================
-- SCRIPT DE INSTALACIÓN COMPLETO PARA ORACLE
-- ============================================================================

-- PASO 1: Crear tabla de auditoría
@trg_auditoria_clientes.sql

-- PASO 2: Crear procedimiento almacenado
@sp_finalizar_compra_web.sql

-- ============================================================================
-- EJEMPLOS DE USO
-- ============================================================================

-- Ejemplo 1: Llamar al procedimiento almacenado
/*
DECLARE
    v_factura_id VARCHAR2(20);
    v_success NUMBER;
    v_message VARCHAR2(500);
BEGIN
    sp_finalizar_compra_web(
        p_carrito_id => 'CRC001',
        p_factura_id => v_factura_id,
        p_success => v_success,
        p_message => v_message
    );
    
    DBMS_OUTPUT.PUT_LINE('Success: ' || v_success);
    DBMS_OUTPUT.PUT_LINE('Message: ' || v_message);
    DBMS_OUTPUT.PUT_LINE('Factura ID: ' || v_factura_id);
END;
/
*/

-- Ejemplo 2: Ver el log de auditoría de un cliente
/*
SELECT 
    LOG_ID,
    LOG_CAMPO,
    LOG_VALOR_ANTERIOR,
    LOG_VALOR_NUEVO,
    LOG_FECHA,
    LOG_USUARIO
FROM LOG_CLIENTES
WHERE CLI_CED_RUC = '1234567890001'
ORDER BY LOG_FECHA DESC;
*/

-- Ejemplo 3: Ver todos los cambios de correo en los últimos 30 días
/*
SELECT 
    c.CLI_NOMBRE,
    lc.LOG_VALOR_ANTERIOR AS correo_anterior,
    lc.LOG_VALOR_NUEVO AS correo_nuevo,
    lc.LOG_FECHA,
    lc.LOG_USUARIO
FROM LOG_CLIENTES lc
JOIN CLIENTES c ON lc.CLI_CED_RUC = c.CLI_CED_RUC
WHERE lc.LOG_CAMPO = 'CLI_CORREO'
  AND lc.LOG_FECHA >= SYSDATE - 30
ORDER BY lc.LOG_FECHA DESC;
*/

COMMIT;
