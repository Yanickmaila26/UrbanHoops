-- ============================================================================
-- MASTER SCRIPT FOR DISTRIBUTED DATABASE SETUP
-- Description: Orchestrates the entire distributed database installation
--              Provides instructions and checks before execution
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ╔════════════════════════════════════════════════════════════════╗
PROMPT ║   UrbanHoops - Distributed Database Setup Master Script       ║
PROMPT ║   Version: 1.0                                                 ║
PROMPT ╚════════════════════════════════════════════════════════════════╝

PROMPT
PROMPT This script will guide you through setting up a distributed
PROMPT database architecture across two Oracle PDBs:
PROMPT   - PROD (172.16.8.125)
PROMPT   - COMEE (172.16.18.125)
PROMPT

PROMPT ════════════════════════════════════════════════════════════════
PROMPT PREREQUISITES CHECKLIST
PROMPT ════════════════════════════════════════════════════════════════

PROMPT
PROMPT ✓ Checklist:
PROMPT   [ ] Both PDBs are running and accessible
PROMPT   [ ] Database links exist:
PROMPT       - link_comee (in PROD, connecting to COMEE)
PROMPT       - link_prod (in COMEE, connecting to PROD)
PROMPT   [ ] You have credentials for both u_prod and u_comee users
PROMPT   [ ] Current Laravel application is stopped
PROMPT   [ ] Data is backed up (or can be regenerated via seeders)
PROMPT

PROMPT Press ENTER to continue or Ctrl+C to cancel...
PAUSE

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT EXECUTION PLAN OVERVIEW
PROMPT ════════════════════════════════════════════════════════════════

PROMPT
PROMPT Phase 1: COMEE Setup
PROMPT   └─ 01_create_tables_comee.sql
PROMPT   └─ 02_create_replica_tables_comee.sql
PROMPT   └─ 03_create_synonyms_comee.sql
PROMPT
PROMPT Phase 2: PROD Setup
PROMPT   └─ 04_create_synonyms_prod.sql
PROMPT   └─ 05_triggers_replication_prod.sql
PROMPT
PROMPT Phase 3: COMEE Triggers
PROMPT   └─ 06_triggers_replication_comee.sql
PROMPT
PROMPT Phase 4: Data Migration
PROMPT   └─ 07_migrate_data.sql
PROMPT
PROMPT Phase 5: Verification
PROMPT   └─ 99_verification_queries.sql
PROMPT
PROMPT Phase 6: Cleanup (AFTER TESTING)
PROMPT   └─ 08_drop_original_tables_prod.sql
PROMPT

PROMPT ════════════════════════════════════════════════════════════════
PROMPT EXECUTION INSTRUCTIONS
PROMPT ════════════════════════════════════════════════════════════════

PROMPT
PROMPT STEP 1: Connect to COMEE PDB
PROMPT ─────────────────────────────
PROMPT   sqlplus u_comee/secreto123@172.16.18.125:1521/comee
PROMPT
PROMPT Execute in order:
PROMPT   @database/oracle/distributed/01_create_tables_comee.sql
PROMPT   @database/oracle/distributed/02_create_replica_tables_comee.sql
PROMPT   @database/oracle/distributed/03_create_synonyms_comee.sql
PROMPT

PROMPT STEP 2: Connect to PROD PDB
PROMPT ───────────────────────────
PROMPT   sqlplus u_prod/secreto123@172.16.8.125:1521/prod
PROMPT
PROMPT Execute in order:
PROMPT   @database/oracle/distributed/04_create_synonyms_prod.sql
PROMPT   @database/oracle/distributed/05_triggers_replication_prod.sql
PROMPT

PROMPT STEP 3: Back to COMEE PDB
PROMPT ─────────────────────────
PROMPT   sqlplus u_comee/secreto123@172.16.18.125:1521/comee
PROMPT
PROMPT Execute:
PROMPT   @database/oracle/distributed/06_triggers_replication_comee.sql
PROMPT

PROMPT STEP 4: Data Migration (from PROD)
PROMPT ──────────────────────────────────
PROMPT   sqlplus u_prod/secreto123@172.16.8.125:1521/prod
PROMPT
PROMPT IMPORTANT: Before running migration, disable triggers in COMEE:
PROMPT   -- In COMEE connection:
PROMPT   ALTER TRIGGER trg_productos_insert_repl DISABLE;
PROMPT   ALTER TRIGGER trg_productos_update_repl DISABLE;
PROMPT   ALTER TRIGGER trg_productos_delete_repl DISABLE;
PROMPT   ALTER TRIGGER trg_facturas_insert_repl DISABLE;
PROMPT   ALTER TRIGGER trg_detalle_factura_insert_repl DISABLE;
PROMPT
PROMPT Then execute migration:
PROMPT   @database/oracle/distributed/07_migrate_data.sql
PROMPT
PROMPT After migration, re-enable triggers in COMEE:
PROMPT   ALTER TRIGGER trg_productos_insert_repl ENABLE;
PROMPT   ALTER TRIGGER trg_productos_update_repl ENABLE;
PROMPT   ALTER TRIGGER trg_productos_delete_repl ENABLE;
PROMPT   ALTER TRIGGER trg_facturas_insert_repl ENABLE;
PROMPT   ALTER TRIGGER trg_detalle_factura_insert_repl ENABLE;
PROMPT

PROMPT STEP 5: Verification
PROMPT ────────────────────
PROMPT   sqlplus u_prod/secreto123@172.16.8.125:1521/prod
PROMPT
PROMPT Execute:
PROMPT   @database/oracle/distributed/99_verification_queries.sql
PROMPT
PROMPT Review all test results. All should show '✓' for success.
PROMPT

PROMPT STEP 6: Update Laravel Configuration
PROMPT ────────────────────────────────────
PROMPT   1. Update .env file with COMEE connection details
PROMPT   2. Update config/database.php
PROMPT   3. Test Laravel application:
PROMPT      php artisan migrate:status
PROMPT      Test CRUD operations
PROMPT

PROMPT STEP 7: Cleanup (ONLY AFTER SUCCESSFUL TESTING)
PROMPT ───────────────────────────────────────────────
PROMPT   sqlplus u_prod/secreto123@172.16.8.125:1521/prod
PROMPT
PROMPT Execute:
PROMPT   @database/oracle/distributed/08_drop_original_tables_prod.sql
PROMPT

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT TABLE DISTRIBUTION SUMMARY
PROMPT ════════════════════════════════════════════════════════════════

PROMPT
PROMPT Tables in COMEE (Master):
PROMPT   • clientes
PROMPT   • datos_facturacion
PROMPT   • carritos
PROMPT   • detalle_carrito
PROMPT   • pedidos
PROMPT   • productos (replica)
PROMPT   • facturas (replica)
PROMPT   • detalle_factura (replica)
PROMPT
PROMPT Tables in PROD (Master):
PROMPT   • productos (master - INSERT/UPDATE/DELETE replication)
PROMPT   • facturas (master - INSERT only replication)
PROMPT   • detalle_factura (master - INSERT only replication)
PROMPT   • proveedors
PROMPT   • bodegas
PROMPT   • kardexes
PROMPT   • orden_compras
PROMPT   • transaccions
PROMPT   • categorias
PROMPT   • subcategorias
PROMPT   • usuario_aplicacions
PROMPT   • users
PROMPT   • (all Laravel framework tables)
PROMPT

PROMPT ════════════════════════════════════════════════════════════════
PROMPT REPLICATION STRATEGY
PROMPT ════════════════════════════════════════════════════════════════

PROMPT
PROMPT Bi-directional Replication:
PROMPT
PROMPT PRODUCTOS:
PROMPT   PROD → COMEE: INSERT, UPDATE, DELETE
PROMPT   COMEE → PROD: INSERT, UPDATE, DELETE
PROMPT
PROMPT FACTURAS:
PROMPT   PROD → COMEE: INSERT only
PROMPT   COMEE → PROD: INSERT only
PROMPT
PROMPT DETALLE_FACTURA:
PROMPT   PROD → COMEE: INSERT only
PROMPT   COMEE → PROD: INSERT only
PROMPT

PROMPT ════════════════════════════════════════════════════════════════
PROMPT READY TO BEGIN
PROMPT ════════════════════════════════════════════════════════════════

PROMPT
PROMPT This master script is for reference only.
PROMPT You must execute each script manually in the correct order.
PROMPT
PROMPT Start with STEP 1 above.
PROMPT
PROMPT Good luck! 🚀
PROMPT

EXIT;
