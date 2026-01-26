-- ============================================================================
-- DIAGNÓSTICO COMPLETO DE USUARIOS ORACLE
-- Ejecutar como SYSTEM
-- ============================================================================

SET LINESIZE 200;
SET PAGESIZE 100;
SET SERVEROUTPUT ON;

PROMPT ========================================
PROMPT 1. ESTADO DE USUARIOS
PROMPT ========================================

COL USERNAME FORMAT A20;
COL ACCOUNT_STATUS FORMAT A20;
COL LOCK_DATE FORMAT A12;
COL CREATED FORMAT A12;

SELECT 
    USERNAME,
    ACCOUNT_STATUS,
    LOCK_DATE,
    CREATED,
    PROFILE
FROM DBA_USERS
WHERE USERNAME IN ('U_ADMIN_PROD', 'U_GESTOR_INV', 'U_APP_FRONTEND')
ORDER BY USERNAME;

PROMPT
PROMPT ========================================
PROMPT 2. PRIVILEGIOS DE SISTEMA
PROMPT ========================================

COL GRANTEE FORMAT A20;
COL PRIVILEGE FORMAT A30;

SELECT 
    GRANTEE,
    PRIVILEGE,
    ADMIN_OPTION
FROM DBA_SYS_PRIVS
WHERE GRANTEE IN ('U_ADMIN_PROD', 'U_GESTOR_INV', 'U_APP_FRONTEND')
ORDER BY GRANTEE, PRIVILEGE;

PROMPT
PROMPT ========================================
PROMPT 3. ROLES ASIGNADOS
PROMPT ========================================

COL GRANTED_ROLE FORMAT A30;

SELECT 
    GRANTEE,
    GRANTED_ROLE,
    ADMIN_OPTION,
    DEFAULT_ROLE
FROM DBA_ROLE_PRIVS
WHERE GRANTEE IN ('U_ADMIN_PROD', 'U_GESTOR_INV', 'U_APP_FRONTEND')
ORDER BY GRANTEE, GRANTED_ROLE;

PROMPT
PROMPT ========================================
PROMPT 4. PERMISOS SOBRE TABLAS (muestra primeros 10)
PROMPT ========================================

COL OWNER FORMAT A10;
COL TABLE_NAME FORMAT A20;
COL GRANTOR FORMAT A15;
COL GRANTEE FORMAT A20;
COL PRIVILEGE FORMAT A15;

SELECT * FROM (
    SELECT 
        OWNER,
        TABLE_NAME,
        GRANTEE,
        PRIVILEGE
    FROM DBA_TAB_PRIVS
    WHERE GRANTEE IN ('ROLE_ADMIN_PROD', 'ROLE_GESTOR_INV', 'ROLE_APP_FRONTEND')
    AND OWNER = 'U_PROD'
    ORDER BY GRANTEE, TABLE_NAME, PRIVILEGE
) WHERE ROWNUM <= 10;

PROMPT
PROMPT ========================================
PROMPT 5. VERIFICACIÓN DE ROLES
PROMPT ========================================

SELECT ROLE FROM DBA_ROLES 
WHERE ROLE LIKE 'ROLE_%'
ORDER BY ROLE;

PROMPT
PROMPT ========================================
PROMPT DIAGNÓSTICO COMPLETO
PROMPT ========================================
PROMPT
PROMPT Si un usuario aparece como LOCKED, ejecuta:
PROMPT   ALTER USER nombre_usuario ACCOUNT UNLOCK;
PROMPT
PROMPT Si falta CREATE SESSION, ejecuta:
PROMPT   GRANT CREATE SESSION TO nombre_usuario;
PROMPT
PROMPT Si no hay roles asignados, ejecuta:
PROMPT   @database/oracle/install_master.sql
PROMPT
PROMPT Si no hay permisos sobre tablas, ejecuta COMO u_prod:
PROMPT   @database/oracle/setup_roles.sql
PROMPT ========================================

EXIT;
