-- ============================================================================
-- TRIGGERS DE REPLICACIÓN EN COMEE
-- Ejecutar en: COMEE como u_comee
-- ============================================================================

-- No hay triggers de replicación en COMEE porque todos los masters están en PROD.
-- Si se requiere bidireccionalidad, agregar aquí triggers hacia PROD.
-- Por ahora, según requerimiento, la interacción es en PROD.

SET SERVEROUTPUT ON;
PROMPT ════════════════════════════════════════════════════════════════
PROMPT Limpiando Triggers en COMEE (Ya no es master)
PROMPT ════════════════════════════════════════════════════════════════

BEGIN
    FOR t IN (SELECT trigger_name FROM user_triggers WHERE trigger_name LIKE '%REPL%') LOOP
        EXECUTE IMMEDIATE 'DROP TRIGGER ' || t.trigger_name;
        DBMS_OUTPUT.PUT_LINE('Eliminado: ' || t.trigger_name);
    END LOOP;
END;
/

PROMPT ✓ Triggers de replicación eliminados de COMEE

EXIT;
