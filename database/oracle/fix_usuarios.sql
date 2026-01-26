-- ============================================================================
-- CORRECCIÓN AUTOMÁTICA DE USUARIOS
-- Ejecutar como SYSTEM
-- Desbloquea usuarios y otorga privilegios básicos
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT Corrigiendo Usuarios de UrbanHoops
PROMPT ========================================

BEGIN
    -- Usuario 1: U_ADMIN_PROD
    BEGIN
        -- Desbloquear
        EXECUTE IMMEDIATE 'ALTER USER U_ADMIN_PROD ACCOUNT UNLOCK';
        DBMS_OUTPUT.PUT_LINE('✓ U_ADMIN_PROD desbloqueado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE = -1918 THEN
                DBMS_OUTPUT.PUT_LINE('⚠ Usuario U_ADMIN_PROD no existe - ejecuta install_master.sql primero');
            ELSE
                DBMS_OUTPUT.PUT_LINE('✓ U_ADMIN_PROD ya estaba desbloqueado');
            END IF;
    END;
    
    BEGIN
        -- Asegurar privilegios
        EXECUTE IMMEDIATE 'GRANT CREATE SESSION TO U_ADMIN_PROD';
        EXECUTE IMMEDIATE 'GRANT CONNECT TO U_ADMIN_PROD';
        EXECUTE IMMEDIATE 'GRANT RESOURCE TO U_ADMIN_PROD';
        DBMS_OUTPUT.PUT_LINE('✓ Privilegios básicos otorgados a U_ADMIN_PROD');
    EXCEPTION
        WHEN OTHERS THEN NULL;
    END;
    
    BEGIN
        -- Asegurar rol
        EXECUTE IMMEDIATE 'GRANT ROLE_ADMIN_PROD TO U_ADMIN_PROD';
        DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_ADMIN_PROD asignado');
    EXCEPTION
        WHEN OTHERS THEN
            DBMS_OUTPUT.PUT_LINE('⚠ Rol ROLE_ADMIN_PROD no existe - ejecuta install_master.sql primero');
    END;

    DBMS_OUTPUT.PUT_LINE('');
    
    -- Usuario 2: U_GESTOR_INV
    BEGIN
        EXECUTE IMMEDIATE 'ALTER USER U_GESTOR_INV ACCOUNT UNLOCK';
        DBMS_OUTPUT.PUT_LINE('✓ U_GESTOR_INV desbloqueado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE = -1918 THEN
                DBMS_OUTPUT.PUT_LINE('⚠ Usuario U_GESTOR_INV no existe');
            ELSE
                DBMS_OUTPUT.PUT_LINE('✓ U_GESTOR_INV ya estaba desbloqueado');
            END IF;
    END;
    
    BEGIN
        EXECUTE IMMEDIATE 'GRANT CREATE SESSION TO U_GESTOR_INV';
        EXECUTE IMMEDIATE 'GRANT CONNECT TO U_GESTOR_INV';
        DBMS_OUTPUT.PUT_LINE('✓ Privilegios básicos otorgados a U_GESTOR_INV');
    EXCEPTION
        WHEN OTHERS THEN NULL;
    END;
    
    BEGIN
        EXECUTE IMMEDIATE 'GRANT ROLE_GESTOR_INV TO U_GESTOR_INV';
        DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_GESTOR_INV asignado');
    EXCEPTION
        WHEN OTHERS THEN
            DBMS_OUTPUT.PUT_LINE('⚠ Rol ROLE_GESTOR_INV no existe');
    END;

    DBMS_OUTPUT.PUT_LINE('');
    
    -- Usuario 3: U_APP_FRONTEND
    BEGIN
        EXECUTE IMMEDIATE 'ALTER USER U_APP_FRONTEND ACCOUNT UNLOCK';
        DBMS_OUTPUT.PUT_LINE('✓ U_APP_FRONTEND desbloqueado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE = -1918 THEN
                DBMS_OUTPUT.PUT_LINE('⚠ Usuario U_APP_FRONTEND no existe');
            ELSE
                DBMS_OUTPUT.PUT_LINE('✓ U_APP_FRONTEND ya estaba desbloqueado');
            END IF;
    END;
    
    BEGIN
        EXECUTE IMMEDIATE 'GRANT CREATE SESSION TO U_APP_FRONTEND';
        EXECUTE IMMEDIATE 'GRANT CONNECT TO U_APP_FRONTEND';
        DBMS_OUTPUT.PUT_LINE('✓ Privilegios básicos otorgados a U_APP_FRONTEND');
    EXCEPTION
        WHEN OTHERS THEN NULL;
    END;
    
    BEGIN
        EXECUTE IMMEDIATE 'GRANT ROLE_APP_FRONTEND TO U_APP_FRONTEND';
        DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_APP_FRONTEND asignado');
    EXCEPTION
        WHEN OTHERS THEN
            DBMS_OUTPUT.PUT_LINE('⚠ Rol ROLE_APP_FRONTEND no existe');
    END;
END;
/

PROMPT
PROMPT ========================================
PROMPT Correcciones Completadas
PROMPT ========================================
PROMPT
PROMPT Ahora verifica el estado:
PROMPT   @database/oracle/diagnostico_completo.sql
PROMPT
PROMPT Si los usuarios no existen, ejecuta:
PROMPT   @database/oracle/install_master.sql
PROMPT
PROMPT Si falta otorgar permisos sobre tablas, ejecuta COMO u_prod:
PROMPT   @database/oracle/setup_roles.sql
PROMPT ========================================

EXIT;
