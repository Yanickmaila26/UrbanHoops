-- ============================================================================
-- Quick Test Script - Test Database Connectivity
-- Description: Verifies that database links are working before setup
-- Execute From: COMEE as u_comee
-- ============================================================================

SET SERVEROUTPUT ON;
SET LINESIZE 120;

PROMPT ════════════════════════════════════════════════════════════════
PROMPT Testing Database Connectivity and Links
PROMPT ════════════════════════════════════════════════════════════════
PROMPT

-- Test 1: Local connection to COMEE
PROMPT Test 1: Local COMEE connection...
SELECT 'COMEE_OK - You are connected to COMEE successfully' AS status FROM dual;

PROMPT

-- Test 2: Database link to PROD
PROMPT Test 2: Testing database link COMEE -> PROD...
BEGIN
    DECLARE
        v_result VARCHAR2(100);
    BEGIN
        EXECUTE IMMEDIATE 'SELECT ''LINK_OK'' FROM dual@link_prod' INTO v_result;
        DBMS_OUTPUT.PUT_LINE('✓ Database link link_prod is working');
        DBMS_OUTPUT.PUT_LINE('  Result: ' || v_result);
    EXCEPTION
        WHEN OTHERS THEN
            DBMS_OUTPUT.PUT_LINE('✗ Database link link_prod FAILED');
            DBMS_OUTPUT.PUT_LINE('  Error: ' || SQLERRM);
            DBMS_OUTPUT.PUT_LINE('');
            DBMS_OUTPUT.PUT_LINE('  Please create the database link with:');
            DBMS_OUTPUT.PUT_LINE('  CREATE  DATABASE LINK link_prod');
            DBMS_OUTPUT.PUT_LINE('    CONNECT TO u_prod IDENTIFIED BY secreto123');
            DBMS_OUTPUT.PUT_LINE('    USING ''prod'';');
    END;
END;
/

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT
PROMPT If the database link test passed, this database is ready for setup.
PROMPT
PROMPT ════════════════════════════════════════════════════════════════

EXIT;
