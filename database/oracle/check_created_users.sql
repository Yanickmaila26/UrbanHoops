-- ============================================================================
-- SCRIPT: check_created_users.sql
-- Descripción: Verifica si los usuarios del proyecto existen.
-- ============================================================================

SET LINESIZE 200;
SET PAGESIZE 100;
COL USERNAME FORMAT A20;
COL ACCOUNT_STATUS FORMAT A20;
COL DEFAULT_TABLESPACE FORMAT A20;
COL PROFILE FORMAT A20;

SELECT USERNAME, ACCOUNT_STATUS, DEFAULT_TABLESPACE, CREATED, PROFILE
FROM DBA_USERS
WHERE USERNAME IN ('U_ADMIN_PROD', 'U_GESTOR_INV', 'U_APP_FRONTEND');

EXIT;
