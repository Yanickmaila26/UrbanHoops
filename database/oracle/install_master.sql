-- ============================================================================
-- MASTER INSTALLATION SCRIPT
-- Descripción: Instala TODO el sistema de seguridad y roles de UrbanHoops
-- 
-- ORDEN DE EJECUCIÓN:
-- 1. Ejecutar como SYSTEM
-- 2. Luego ejecutar setup_roles.sql COMO u_prod
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT UrbanHoops - Master Installation
PROMPT Paso 1/2: Creando Roles (como SYSTEM)
PROMPT ========================================

BEGIN
    -- Crear los 3 roles del sistema
    BEGIN
        EXECUTE IMMEDIATE 'CREATE ROLE ROLE_ADMIN_PROD';
        DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_ADMIN_PROD creado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE != -1921 THEN 
                RAISE; 
            ELSE
                DBMS_OUTPUT.PUT_LINE('  Rol ROLE_ADMIN_PROD ya existe');
            END IF;
    END;

    BEGIN
        EXECUTE IMMEDIATE 'CREATE ROLE ROLE_GESTOR_INV';
        DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_GESTOR_INV creado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE != -1921 THEN RAISE; 
            ELSE DBMS_OUTPUT.PUT_LINE('  Rol ROLE_GESTOR_INV ya existe');
            END IF;
    END;
    
    BEGIN
        EXECUTE IMMEDIATE 'CREATE ROLE ROLE_APP_FRONTEND';
        DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_APP_FRONTEND creado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE != -1921 THEN RAISE; 
            ELSE DBMS_OUTPUT.PUT_LINE('  Rol ROLE_APP_FRONTEND ya existe');
            END IF;
    END;
END;
/

PROMPT
PROMPT ========================================
PROMPT Paso 2/2: Creando Usuarios
PROMPT ========================================

BEGIN
    -- 1. Usuario U_ADMIN_PROD (Administrador DB)
    BEGIN
        EXECUTE IMMEDIATE 'CREATE USER U_ADMIN_PROD IDENTIFIED BY "Admin123!"';
        EXECUTE IMMEDIATE 'ALTER USER U_ADMIN_PROD ACCOUNT UNLOCK';
        EXECUTE IMMEDIATE 'GRANT CREATE SESSION, CONNECT, RESOURCE TO U_ADMIN_PROD';
        EXECUTE IMMEDIATE 'GRANT ROLE_ADMIN_PROD TO U_ADMIN_PROD';
        DBMS_OUTPUT.PUT_LINE('✓ Usuario U_ADMIN_PROD creado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE = -1920 THEN
                EXECUTE IMMEDIATE 'ALTER USER U_ADMIN_PROD IDENTIFIED BY "Admin123!"';
                EXECUTE IMMEDIATE 'ALTER USER U_ADMIN_PROD ACCOUNT UNLOCK';
                EXECUTE IMMEDIATE 'GRANT CREATE SESSION, CONNECT, RESOURCE TO U_ADMIN_PROD';
                EXECUTE IMMEDIATE 'GRANT ROLE_ADMIN_PROD TO U_ADMIN_PROD';
                DBMS_OUTPUT.PUT_LINE('✓ U_ADMIN_PROD actualizado');
            ELSE RAISE; END IF;
    END;

    -- 2. Usuario U_GESTOR_INV (Gestor de Inventario)
    BEGIN
        EXECUTE IMMEDIATE 'CREATE USER U_GESTOR_INV IDENTIFIED BY "Gestor123!"';
        EXECUTE IMMEDIATE 'ALTER USER U_GESTOR_INV ACCOUNT UNLOCK';
        EXECUTE IMMEDIATE 'GRANT CREATE SESSION, CONNECT TO U_GESTOR_INV';
        EXECUTE IMMEDIATE 'GRANT ROLE_GESTOR_INV TO U_GESTOR_INV';
        DBMS_OUTPUT.PUT_LINE('✓ Usuario U_GESTOR_INV creado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE = -1920 THEN
                EXECUTE IMMEDIATE 'ALTER USER U_GESTOR_INV IDENTIFIED BY "Gestor123!"';
                EXECUTE IMMEDIATE 'ALTER USER U_GESTOR_INV ACCOUNT UNLOCK';
                EXECUTE IMMEDIATE 'GRANT CREATE SESSION, CONNECT TO U_GESTOR_INV';
                EXECUTE IMMEDIATE 'GRANT ROLE_GESTOR_INV TO U_GESTOR_INV';
                DBMS_OUTPUT.PUT_LINE('✓ U_GESTOR_INV actualizado');
            ELSE RAISE; END IF;
    END;
    
    -- 3. Usuario U_APP_FRONTEND (Cliente Frontend)
    BEGIN
        EXECUTE IMMEDIATE 'CREATE USER U_APP_FRONTEND IDENTIFIED BY "Front123!"';
        EXECUTE IMMEDIATE 'ALTER USER U_APP_FRONTEND ACCOUNT UNLOCK';
        EXECUTE IMMEDIATE 'GRANT CREATE SESSION, CONNECT TO U_APP_FRONTEND';
        EXECUTE IMMEDIATE 'GRANT ROLE_APP_FRONTEND TO U_APP_FRONTEND';
        DBMS_OUTPUT.PUT_LINE('✓ Usuario U_APP_FRONTEND creado');
    EXCEPTION
        WHEN OTHERS THEN
            IF SQLCODE = -1920 THEN
                EXECUTE IMMEDIATE 'ALTER USER U_APP_FRONTEND IDENTIFIED BY "Front123!"';
                EXECUTE IMMEDIATE 'ALTER USER U_APP_FRONTEND ACCOUNT UNLOCK';
                EXECUTE IMMEDIATE 'GRANT CREATE SESSION, CONNECT TO U_APP_FRONTEND';
                EXECUTE IMMEDIATE 'GRANT ROLE_APP_FRONTEND TO U_APP_FRONTEND';
                DBMS_OUTPUT.PUT_LINE('✓ U_APP_FRONTEND actualizado');
            ELSE RAISE; END IF;
    END;
END;
/

PROMPT
PROMPT ========================================
PROMPT ✅ Usuarios y Roles creados
PROMPT ========================================
PROMPT
PROMPT ⚠️  IMPORTANTE: Ahora debes ejecutar COMO u_prod:
PROMPT     @database/oracle/setup_roles.sql
PROMPT
PROMPT Esto otorgará los permisos sobre las tablas.
PROMPT ========================================

EXIT;
