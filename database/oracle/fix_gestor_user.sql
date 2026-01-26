-- ============================================================================
-- SCRIPT: fix_gestor_user.sql
-- Descripción: Corrige problemas de conexión para U_GESTOR_INV
-- Ejecutar como SYSTEM o ADMIN
-- ============================================================================

SET SERVEROUTPUT ON;

BEGIN
    -- Desbloquear usuario
    EXECUTE IMMEDIATE 'ALTER USER U_GESTOR_INV ACCOUNT UNLOCK';
    DBMS_OUTPUT.PUT_LINE('✓ Usuario U_GESTOR_INV desbloqueado');
    
    -- Asegurar CREATE SESSION
    EXECUTE IMMEDIATE 'GRANT CREATE SESSION TO U_GESTOR_INV';
    DBMS_OUTPUT.PUT_LINE('✓ Privilegio CREATE SESSION otorgado');
    
    -- Asegurar rol
    EXECUTE IMMEDIATE 'GRANT ROLE_GESTOR_INV TO U_GESTOR_INV';
    DBMS_OUTPUT.PUT_LINE('✓ Rol ROLE_GESTOR_INV asignado');
    
    -- Reiniciar password por si acaso
    EXECUTE IMMEDIATE 'ALTER USER U_GESTOR_INV IDENTIFIED BY "Gestor123!"';
    DBMS_OUTPUT.PUT_LINE('✓ Contraseña restablecida: Gestor123!');
    
    DBMS_OUTPUT.PUT_LINE('');
    DBMS_OUTPUT.PUT_LINE('=== Usuario U_GESTOR_INV corregido ===');
    DBMS_OUTPUT.PUT_LINE('Usuario: U_GESTOR_INV');
    DBMS_OUTPUT.PUT_LINE('Password: Gestor123!');
END;
/

-- Verificar estado final
SELECT USERNAME, ACCOUNT_STATUS, LOCK_DATE 
FROM DBA_USERS 
WHERE USERNAME = 'U_GESTOR_INV';

EXIT;
