-- ============================================================================
-- TRIGGER: trg_auditoria_clientes
-- Descripción: Registra cambios en correo y teléfono de clientes
-- NOTA: CLI_DIRECCION no existe en la tabla CLIENTES
-- ============================================================================

CREATE OR REPLACE TRIGGER trg_auditoria_clientes
BEFORE UPDATE ON CLIENTES
FOR EACH ROW
DECLARE
    v_usuario VARCHAR2(100);
BEGIN
    v_usuario := SYS_CONTEXT('USERENV', 'SESSION_USER');
    
    -- Auditar cambio de correo
    IF NVL(:OLD.CLI_CORREO, 'X') != NVL(:NEW.CLI_CORREO, 'X') THEN
        INSERT INTO LOG_CLIENTES (
            CLI_CED_RUC, LOG_CAMPO, LOG_VALOR_ANTERIOR,
            LOG_VALOR_NUEVO, LOG_FECHA, LOG_USUARIO
        ) VALUES (
            :OLD.CLI_CED_RUC, 'CLI_CORREO', :OLD.CLI_CORREO,
            :NEW.CLI_CORREO, SYSTIMESTAMP, v_usuario
        );
    END IF;
    
    -- Auditar cambio de teléfono
    IF NVL(:OLD.CLI_TELEFONO, 'X') != NVL(:NEW.CLI_TELEFONO, 'X') THEN
        INSERT INTO LOG_CLIENTES (
            CLI_CED_RUC, LOG_CAMPO, LOG_VALOR_ANTERIOR,
            LOG_VALOR_NUEVO, LOG_FECHA, LOG_USUARIO
        ) VALUES (
            :OLD.CLI_CED_RUC, 'CLI_TELEFONO', :OLD.CLI_TELEFONO,
            :NEW.CLI_TELEFONO, SYSTIMESTAMP, v_usuario
        );
    END IF;
    
    -- Auditar cambio de nombre
    IF NVL(:OLD.CLI_NOMBRE, 'X') != NVL(:NEW.CLI_NOMBRE, 'X') THEN
        INSERT INTO LOG_CLIENTES (
            CLI_CED_RUC, LOG_CAMPO, LOG_VALOR_ANTERIOR,
            LOG_VALOR_NUEVO, LOG_FECHA, LOG_USUARIO
        ) VALUES (
            :OLD.CLI_CED_RUC, 'CLI_NOMBRE', :OLD.CLI_NOMBRE,
            :NEW.CLI_NOMBRE, SYSTIMESTAMP, v_usuario
        );
    END IF;
END;
/
