-- ============================================================================
-- Quick Test Script - Test Database Connectivity
-- Description: Verifies that database links are working before setup
-- Execute From: PROD as u_prod
-- ============================================================================

SET SERVEROUTPUT ON;
SET LINESIZE 120;

PROMPT ════════════════════════════════════════════════════════════════
PROMPT Testing Database Connectivity and Links
PROMPT ════════════════════════════════════════════════════════════════
PROMPT

-- Test 1: Local connection to PROD
PROMPT Test 1: Local PROD connection...
SELECT 'PROD_OK - You are connected to PROD successfully' AS status FROM dual;

PROMPT

-- Test 2: Database link to COMEE
PROMPT Test 2: Testing database link PROD -> COMEE...
BEGIN
    DECLARE
        v_result VARCHAR2(100);
    BEGIN
        EXECUTE IMMEDIATE 'SELECT ''LINK_OK'' FROM dual@link_comee' INTO v_result;
        DBMS_OUTPUT.PUT_LINE('✓ Database link link_comee is working');
        DBMS_OUTPUT.PUT_LINE('  Result: ' || v_result);
    EXCEPTION
        WHEN OTHERS THEN
            DBMS_OUTPUT.PUT_LINE('✗ Database link link_comee FAILED');
            DBMS_OUTPUT.PUT_LINE('  Error: ' || SQLERRM);
            DBMS_OUTPUT.PUT_LINE('');
            DBMS_OUTPUT.PUT_LINE('  Please create the database link with:');
            DBMS_OUTPUT.PUT_LINE('  CREATE DATABASE LINK link_comee');
            DBMS_OUTPUT.PUT_LINE('    CONNECT TO u_comee IDENTIFIED BY secreto123');
            DBMS_OUTPUT.PUT_LINE('    USING ''comee'';');
    END;
END;
/

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT
PROMPT If the database link test passed, you can proceed with setup.
PROMPT
PROMPT Next: Execute scripts in COMEE first:
PROMPT   sqlplus u_comee/secreto123@172.16.18.125:1521/comee
PROMPT   @database/oracle/distributed/01_create_tables_comee.sql
PROMPT
PROMPT ════════════════════════════════════════════════════════════════

EXIT;
