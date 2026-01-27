# Guía de Ejecución Paso a Paso - Base de Datos Distribuida

## ✅ Paso 0: Verificar Prerequisitos

Antes de comenzar, asegúrate de tener:

1. **Ambos PDBs funcionando:**
   - PROD: 192.168.1.115:1521/prod (usuario: u_prod)
   - COMEE: 192.168.1.125:1521/comee (usuario: u_comee)

2. **Database links creados:**
   - En PROD: `link_comee` apuntando a COMEE
   - En COMEE: `link_prod` apuntando a PROD

3. **Aplicación Laravel detenida**

4. **SQL*Plus instalado** (o SQL Developer)

---

## 🔍 Paso 1: Probar Conectividad

### Opción A: Usando SQL*Plus (Recomendado)

#### Test PROD:
```bash
cd c:\laragon\www\UrbanHoops
sqlplus u_prod/secreto123@192.168.1.115:1521/prod @database\oracle\distributed\test-connection-prod.sql
```

**Resultado esperado:** ✓ Database link link_comee is working

#### Test COMEE:
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee @database\oracle\distributed\test-connection-comee.sql
```

**Resultado esperado:** ✓ Database link link_prod is working

### Opción B: Usando el comando migrate de Laravel

```bash
php artisan migrate:status
```

Si puedes ver las migraciones, la conexión funciona.

---

## 📋 Paso 2: Ejecutar Scripts en COMEE

### 2.1 - Crear Tablas Principales

**Conectarse a COMEE:**
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee
```

**Ejecutar:**
```sql
@database\oracle\distributed\01_create_tables_comee.sql
```

**Resultado esperado:**
```
✓ Table CLIENTES created
✓ Table DATOS_FACTURACION created
✓ Table CARRITOS created
✓ Table DETALLE_CARRITO created
✓ Table PEDIDOS created
```

### 2.2 - Crear Tablas de Réplica

**Sin salir de la sesión SQL*Plus (o reconectarse):**
```sql
@database\oracle\distributed\02_create_replica_tables_comee.sql
```

**Resultado esperado:**
```
✓ Table PRODUCTOS (replica) created
✓ Table FACTURAS (replica) created
✓ Table DETALLE_FACTURA (replica) created
✓ Foreign key added
```

### 2.3 - Crear Sinónimos

**Sin salir de la sesión SQL*Plus:**
```sql
@database\oracle\distributed\03_create_synonyms_comee.sql
```

**Resultado esperado:**
```
✓ Database link LINK_PROD is active and working
✓ Synonym: proveedors -> u_prod.proveedors@link_prod
✓ Synonym: bodegas -> u_prod.bodegas@link_prod
... (20+ sinónimos más)
```

**Ahora puedes salir:** `exit`

---

## 📋 Paso 3: Ejecutar Scripts en PROD

### 3.1 - Crear Sinónimos

**Conectarse a PROD:**
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod
```

**Ejecutar:**
```sql
@database\oracle\distributed\04_create_synonyms_prod.sql
```

**Resultado esperado:**
```
✓ Database link LINK_COMEE is active and working
✓ Synonym: clientes -> u_comee.clientes@link_comee
✓ Synonym: datos_facturacion -> u_comee.datos_facturacion@link_comee
✓ Synonym: carritos -> u_comee.carritos@link_comee
✓ Synonym: detalle_carrito -> u_comee.detalle_carrito@link_comee
✓ Synonym: pedidos -> u_comee.pedidos@link_comee
```

### 3.2 - Crear Triggers de Replicación

**Sin salir de la sesión SQL*Plus:**
```sql
@database\oracle\distributed\05_triggers_replication_prod.sql
```

**Resultado esperado:**
```
✓ Trigger TRG_PRODUCTOS_INSERT_REPL created
✓ Trigger TRG_PRODUCTOS_UPDATE_REPL created
✓ Trigger TRG_PRODUCTOS_DELETE_REPL created
✓ Trigger TRG_FACTURAS_INSERT_REPL created
✓ Trigger TRG_DETALLE_FACTURA_INSERT_REPL created
```

**Ahora puedes salir:** `exit`

---

## 📋 Paso 4: Completar Setup en COMEE

### 4.1 - Crear Triggers de Replicación

**Conectarse a COMEE:**
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee
```

**Ejecutar:**
```sql
@database\oracle\distributed\06_triggers_replication_comee.sql
```

**Resultado esperado:**
```
✓ Trigger TRG_PRODUCTOS_INSERT_REPL created
✓ Trigger TRG_PRODUCTOS_UPDATE_REPL created
✓ Trigger TRG_PRODUCTOS_DELETE_REPL created
✓ Trigger TRG_FACTURAS_INSERT_REPL created
✓ Trigger TRG_DETALLE_FACTURA_INSERT_REPL created
```

**Ahora puedes salir:** `exit`

---

## 📋 Paso 5: Migrar Datos Existentes

> **⚠️ IMPORTANTE:** Este paso requiere deshabilitar triggers temporalmente para evitar bucles de replicación.

### 5.1 - Deshabilitar Triggers en COMEE

**Conectarse a COMEE:**
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee
```

**Ejecutar estos comandos:**
```sql
ALTER TRIGGER trg_productos_insert_repl DISABLE;
ALTER TRIGGER trg_productos_update_repl DISABLE;
ALTER TRIGGER trg_productos_delete_repl DISABLE;
ALTER TRIGGER trg_facturas_insert_repl DISABLE;
ALTER TRIGGER trg_detalle_factura_insert_repl DISABLE;
exit;
```

### 5.2 - Ejecutar Migración desde PROD

**Conectarse a PROD:**
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod
```

**Ejecutar:**
```sql
@database\oracle\distributed\07_migrate_data.sql
```

**El script te pedirá confirmación para deshabilitar triggers en COMEE. YA LO HICISTE en el paso anterior, así que presiona ENTER para continuar.**

**Resultado esperado:**
```
✓ Triggers disabled in PROD
✓ Migrated X rows to CLIENTES@link_comee
✓ Migrated X rows to DATOS_FACTURACION@link_comee
✓ Migrated X rows to CARRITOS@link_comee
✓ Migrated X rows to DETALLE_CARRITO@link_comee
✓ Migrated X rows to PEDIDOS@link_comee
✓ Replicated X rows to PRODUCTOS@link_comee
✓ Replicated X rows to FACTURAS@link_comee

Verification Results:
✓ clientes: X rows (match)
✓ carritos: X rows (match)
...
✓ Triggers enabled in PROD
```

**Salir:** `exit`

### 5.3 - Reactivar Triggers en COMEE

**Conectarse a COMEE:**
```bash
sqlplus u_comee/secreto123@192.168.1.125:1521/comee
```

**Ejecutar:**
```sql
ALTER TRIGGER trg_productos_insert_repl ENABLE;
ALTER TRIGGER trg_productos_update_repl ENABLE;
ALTER TRIGGER trg_productos_delete_repl ENABLE;
ALTER TRIGGER trg_facturas_insert_repl ENABLE;
ALTER TRIGGER trg_detalle_factura_insert_repl ENABLE;
exit;
```

---

## 🧪 Paso 6: Verificación

### 6.1 - Ejecutar Tests Automáticos

**Conectarse a PROD:**
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod
```

**Ejecutar:**
```sql
@database\oracle\distributed\99_verification_queries.sql
```

**Resultados esperados:**
```
TEST 1: Database Link Connectivity
✓ PROD->COMEE: OK

TEST 2: Synonym Resolution
✓ Synonyms working

TEST 3: Replica Table Row Count Comparison
✓ productos: MATCH
✓ facturas: MATCH

TEST 4: Testing Productos INSERT Trigger
✓ Product replicated successfully to COMEE

TEST 5: Testing Productos UPDATE Trigger
✓ Update replicated

TEST 6: Foreign Key Integrity Test
✓ Foreign key constraint working correctly

TEST 7: Cleaning Up Test Data
✓ Delete replicated successfully to COMEE
```

### 6.2 - Probar con Laravel

**Salir de SQL*Plus:** `exit`

**Ejecutar en PowerShell:**
```bash
# Verificar estado de migraciones
php artisan migrate:status

# Probar conexión
php artisan tinker
```

**En Tinker:**
```php
// Esto debería funcionar sin errores
App\Models\Cliente::count();
App\Models\Producto::count();
App\Models\Carrito::count();

// Crear un cliente de prueba (irá a COMEE)
$cliente = new App\Models\Cliente;
$cliente->CLI_Ced_Ruc = '9999999999999';
$cliente->CLI_Nombre = 'Test Cliente';
$cliente->CLI_Telefono = '0999999999';
$cliente->CLI_Correo = 'test@test.com';
$cliente->save();

// Verificar que existe
App\Models\Cliente::where('CLI_Ced_Ruc', '9999999999999')->first();

exit
```

---

## 🧹 Paso 7: Limpieza (OPCIONAL - Solo después de probar todo)

Una vez que hayas probado y verificado que todo funciona correctamente durante al menos unos días, puedes eliminar las tablas originales de PROD para liberar espacio:

**Conectarse a PROD:**
```bash
sqlplus u_prod/secreto123@192.168.1.115:1521/prod
```

**Ejecutar:**
```sql
@database\oracle\distributed\08_drop_original_tables_prod.sql
```

> **⚠️ ADVERTENCIA:** Este paso es DESTRUCTIVO. Solo ejecutar después de confirmar que:
> - Todos los sinónimos funcionan correctamente
> - La aplicación Laravel funciona sin errores
> - Los triggers replican datos correctamente
> - Has probado crear/editar/eliminar registros

---

## 🎯 Resumen de Archivos

| Script | Dónde Ejecutar | Qué Hace |
|--------|---------------|----------|
| test-connection-prod.sql | PROD | Prueba conectividad y database link |
| test-connection-comee.sql | COMEE | Prueba conectividad y database link |
| 01_create_tables_comee.sql | COMEE | Crea tablas principales (clientes, carritos, pedidos) |
| 02_create_replica_tables_comee.sql | COMEE | Crea tablas réplica (productos, facturas) |
| 03_create_synonyms_comee.sql | COMEE | Crea sinónimos hacia PROD |
| 04_create_synonyms_prod.sql | PROD | Crea sinónimos hacia COMEE |
| 05_triggers_replication_prod.sql | PROD | Triggers de replicación PROD→COMEE |
| 06_triggers_replication_comee.sql | COMEE | Triggers de replicación COMEE→PROD |
| 07_migrate_data.sql | PROD | Migra datos existentes |
| 08_drop_original_tables_prod.sql | PROD | Limpia tablas (después de testing) |
| 99_verification_queries.sql | PROD | Tests de verificación |

---

## 🆘 Troubleshooting

### Error: "ORA-02019: connection description for remote database not found"
**Solución:** El database link no existe. Crearlo con:
```sql
CREATE DATABASE LINK link_comee 
  CONNECT TO u_comee IDENTIFIED BY secreto123 
  USING 'comee';
```

### Error: "ORA-00942: table or view does not exist"
**Solución:** Verificar que los scripts anteriores se ejecutaron correctamente.

### Error: "ORA-02291: integrity constraint violated - parent key not found"
**Solución:** El foreign key está funcionando correctamente (es el comportamiento esperado).

### Los datos no se replican después de INSERT
**Solución:** Verificar que los triggers estén habilitados:
```sql
SELECT trigger_name, status FROM user_triggers WHERE trigger_name LIKE '%REPL%';
```

Si están DISABLED, habilitarlos con:
```sql
ALTER TRIGGER nombre_trigger ENABLE;
```

---

## 📞 Siguientes Pasos

Una vez completada la instalación:

1. **Usar normalmente `php artisan migrate:fresh-oracle --seed`**
   - Los seeders poblarán ambas bases de datos automáticamente
   - Los triggers mantendrán las réplicas sincronizadas

2. **Monitorear el rendimiento**
   - Observa el tiempo de respuesta de queries distribuidos
   - Revisa los logs de Oracle para errores en triggers

3. **Backup regular**
   - Realiza backups de ambas bases de datos
   - Considera script de re-sincronización en caso de divergencia

4. **Documentación**
   - Lee [README.md](file:///c:/laragon/www/UrbanHoops/database/oracle/distributed/README.md) para detalles de mantenimiento
   - Consulta [walkthrough.md](file:///C:/Users/PC/.gemini/antigravity/brain/5ab77880-af27-4c94-a906-58be94c852b4/walkthrough.md) para arquitectura detallada
