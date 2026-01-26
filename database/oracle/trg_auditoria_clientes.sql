-- ============================================================================
-- TRIGGER: trg_auditoria_clientes
-- Descripción: Registra cambios en correo y teléfono de clientes
-- NOTA: CLI_DIRECCION no existe en la tabla CLIENTES
-- ============================================================================

CREATE OR REPLACE TRIGGER trg_auditoria_clientes
BEFORE UPDATE ON "clientes"
FOR EACH ROW
DECLARE
    v_usuario VARCHAR2(100);
BEGIN
    v_usuario := SYS_CONTEXT('USERENV', 'SESSION_USER');
    
    -- Auditar cambio de correo
    IF NVL(:OLD."CLI_Correo", 'X') != NVL(:NEW."CLI_Correo", 'X') THEN
        INSERT INTO LOG_CLIENTES (
            CLI_CED_RUC, LOG_CAMPO, LOG_VALOR_ANTERIOR,
            LOG_VALOR_NUEVO, LOG_FECHA, LOG_USUARIO
        ) VALUES (
            :OLD."CLI_Ced_Ruc", 'CLI_Correo', :OLD."CLI_Correo",
            :NEW."CLI_Correo", SYSTIMESTAMP, v_usuario
        );
    END IF;
    
    -- Auditar cambio de teléfono
    IF NVL(:OLD."CLI_Telefono", 'X') != NVL(:NEW."CLI_Telefono", 'X') THEN
        INSERT INTO LOG_CLIENTES (
            CLI_CED_RUC, LOG_CAMPO, LOG_VALOR_ANTERIOR,
            LOG_VALOR_NUEVO, LOG_FECHA, LOG_USUARIO
        ) VALUES (
            :OLD."CLI_Ced_Ruc", 'CLI_Telefono', :OLD."CLI_Telefono",
            :NEW."CLI_Telefono", SYSTIMESTAMP, v_usuario
        );
    END IF;
    
    -- Auditar cambio de nombre
    IF NVL(:OLD."CLI_Nombre", 'X') != NVL(:NEW."CLI_Nombre", 'X') THEN
        INSERT INTO LOG_CLIENTES (
            CLI_CED_RUC, LOG_CAMPO, LOG_VALOR_ANTERIOR,
            LOG_VALOR_NUEVO, LOG_FECHA, LOG_USUARIO
        ) VALUES (
            :OLD."CLI_Ced_Ruc", 'CLI_Nombre', :OLD."CLI_Nombre",
            :NEW."CLI_Nombre", SYSTIMESTAMP, v_usuario
        );
    END IF;
END;
/
