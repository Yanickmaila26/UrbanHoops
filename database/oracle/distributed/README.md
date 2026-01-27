# Distributed Database Setup Guide

## Overview

This guide explains how to set up and maintain the distributed Oracle database architecture for UrbanHoops across two PDBs:
- **PROD**: 192.168.1.115:1521/prod
- **COMEE**: 192.168.1.125:1521/comee

## Table Distribution

### Tables in COMEE (Master)
- `clientes` - Customer data
- `datos_facturacion` - Billing/shipping information
- `carritos` - Shopping carts
- `detalle_carrito` - Cart line items
- `pedidos` - Orders

### Replicated Tables (in both PDBs)
- `productos` - Products (bi-directional: INSERT/UPDATE/DELETE)
- `facturas` - Invoices (bi-directional: INSERT only)
- `detalle_factura` - Invoice line items (bi-directional: INSERT only)

### Tables in PROD (Master)
All other tables including:
- `proveedors`, `bodegas`, `kardexes`
- `orden_compras`, `transaccions`
- `categorias`, `subcategorias`
- Laravel framework tables (users, migrations, cache, etc.)

## Installation Steps

### Prerequisites
1. Both PDBs running and accessible
2. Database links configured:
   - `link_comee` in PROD → COMEE
   - `link_prod` in COMEE → PROD
3. Laravel application stopped
4. Data backed up (or can be regenerated via seeders)

### Execution Order

#### Phase 1: Setup COMEE
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee

@database/oracle/distributed/01_create_tables_comee.sql
@database/oracle/distributed/02_create_replica_tables_comee.sql
@database/oracle/distributed/03_create_synonyms_comee.sql
```

#### Phase 2: Setup PROD
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod

@database/oracle/distributed/04_create_synonyms_prod.sql
@database/oracle/distributed/05_triggers_replication_prod.sql
```

#### Phase 3: Complete COMEE Setup
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee

@database/oracle/distributed/06_triggers_replication_comee.sql
```

#### Phase 4: Migrate Data

**Step 1**: Disable triggers in COMEE
```sql
ALTER TRIGGER trg_productos_insert_repl DISABLE;
ALTER TRIGGER trg_productos_update_repl DISABLE;
ALTER TRIGGER trg_productos_delete_repl DISABLE;
ALTER TRIGGER trg_facturas_insert_repl DISABLE;
ALTER TRIGGER trg_detalle_factura_insert_repl DISABLE;
```

**Step 2**: Run migration from PROD
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod

@database/oracle/distributed/07_migrate_data.sql
```

**Step 3**: Re-enable triggers in COMEE
```sql
ALTER TRIGGER trg_productos_insert_repl ENABLE;
ALTER TRIGGER trg_productos_update_repl ENABLE;
ALTER TRIGGER trg_productos_delete_repl ENABLE;
ALTER TRIGGER trg_facturas_insert_repl ENABLE;
ALTER TRIGGER trg_detalle_factura_insert_repl ENABLE;
```

#### Phase 5: Verify
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod

@database/oracle/distributed/99_verification_queries.sql
```

All tests should show ✓ for success.

#### Phase 6: Test Laravel Application

1. Start Laravel application
2. Test key operations:
   - Create customer
   - Add items to cart
   - Create product
   - Generate invoice
3. Verify data appears in both databases

#### Phase 7: Cleanup (ONLY AFTER SUCCESSFUL TESTING)
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod

@database/oracle/distributed/08_drop_original_tables_prod.sql
```

## Laravel Integration

The application uses database synonyms for transparent access. No code changes required. Laravel will automatically:
- Access `clientes`, `carritos`, etc. through synonyms (which point to COMEE)
- Access `productos`, `facturas` in PROD (master copies)
- Triggers handle replication automatically

## Replication Details

### Productos (Full CRUD Replication)
- **INSERT**: New products replicate to both PDBs
- **UPDATE**: Price, stock, and attribute changes sync both ways
- **DELETE**: Product deletions replicate to both PDBs

Use case: Products can be managed from either database

### Facturas (INSERT-only Replication)
- **INSERT**: New invoices replicate to both PDBs
- **UPDATE/DELETE**: NOT replicated

Use case: Invoices are immutable once created

## Troubleshooting

### Database Link Issues
```sql
-- Test connectivity
SELECT * FROM dual@link_comee;  -- From PROD
SELECT * FROM dual@link_prod;   -- From COMEE
```

### Synonym Resolution
```sql
-- Verify synonym target
SELECT table_name, db_link FROM user_synonyms WHERE table_name = 'CLIENTES';
```

### Trigger Status
```sql
-- Check trigger status
SELECT trigger_name, status FROM user_triggers 
WHERE trigger_name LIKE '%REPL%';

-- Enable/disable triggers
ALTER TRIGGER trg_productos_insert_repl ENABLE;
ALTER TRIGGER trg_productos_insert_repl DISABLE;
```

### Replication Lag
Replication is synchronous (happens in same transaction). If triggers fail:
1. Check trigger status
2. Verify database link connectivity
3. Check Oracle alert logs
4. Review trigger code for errors

## Using with Laravel Migrations

When running `php artisan migrate:fresh-oracle --seed`:

1. **First time setup**: Run all distributed database scripts first
2. **Regular resets**: Use the migrate command normally - synonyms will route to correct PDB
3. **Seeding**: Seeders will populate both databases automatically via triggers

## Maintenance

### Re-synchronizing Data
If replicas get out of sync:

```sql
-- From PROD, disable triggers in both PDBs first
-- Then re-sync productos
DELETE FROM productos@link_comee;
INSERT INTO productos@link_comee SELECT * FROM productos;

-- Re-sync facturas
DELETE FROM facturas@link_comee;
INSERT INTO facturas@link_comee SELECT * FROM facturas;

-- Re-enable triggers
```

### Monitoring Replication
No built-in monitoring. Consider adding:
- Error logging table
- Replication lag tracking
- Alert on trigger failures

## Performance Considerations

- Database link queries are slower than local queries
- Triggers add overhead to INSERT/UPDATE/DELETE operations
- Consider connection pooling for database links
- Monitor network latency between VMs

## Security Notes

- Database link credentials stored in database
- Use encrypted connections for production
- Restrict database link usage to necessary users
- Audit distributed transactions
