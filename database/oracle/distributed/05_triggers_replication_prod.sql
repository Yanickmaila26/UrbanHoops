-- ============================================================================
-- TRIGGERS DE REPLICACIÓN EN PROD - COLUMNAS CORRECTAS
-- Ejecutar en: PROD como u_prod
-- MASTER PARA: PRODUCTOS, CLIENTES, FACTURAS
-- ============================================================================

SET SERVEROUTPUT ON;

PROMPT ════════════════════════════════════════════════════════════════
PROMPT Creando Triggers de Replicación en PROD (Master -> Replica)
PROMPT ════════════════════════════════════════════════════════════════

-- ============================================================================
-- PRODUCTOS: PROD -> COMEE
-- ============================================================================

PROMPT Creando triggers para PRODUCTOS (PROD -> COMEE)...

CREATE OR REPLACE TRIGGER trg_productos_insert_repl
AFTER INSERT ON productos
FOR EACH ROW
BEGIN
    INSERT INTO productos@link_comee (
        PRO_Codigo, PRV_Ced_Ruc, SCT_Codigo, PRO_Nombre, PRO_Descripcion,
        PRO_Color, PRO_Talla, PRO_Marca, PRO_Precio, PRO_Stock,
        PRO_Imagen, ACTIVO, CREATED_AT, UPDATED_AT
    ) VALUES (
        :NEW.PRO_Codigo, :NEW.PRV_Ced_Ruc, :NEW.SCT_Codigo, :NEW.PRO_Nombre, :NEW.PRO_Descripcion,
        :NEW.PRO_Color, :NEW.PRO_Talla, :NEW.PRO_Marca, :NEW.PRO_Precio, :NEW.PRO_Stock,
        :NEW.PRO_Imagen, :NEW.ACTIVO, :NEW.CREATED_AT, :NEW.UPDATED_AT
    );
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN NULL;
    WHEN OTHERS THEN NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_productos_update_repl
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    UPDATE productos@link_comee p SET p.PRV_Ced_Ruc = :NEW.PRV_Ced_Ruc, p.SCT_Codigo = :NEW.SCT_Codigo, p.PRO_Nombre = :NEW.PRO_Nombre, p.PRO_Descripcion = :NEW.PRO_Descripcion, p.PRO_Color = :NEW.PRO_Color, p.PRO_Talla = :NEW.PRO_Talla, p.PRO_Marca = :NEW.PRO_Marca, p.PRO_Precio = :NEW.PRO_Precio, p.PRO_Stock = :NEW.PRO_Stock, p.PRO_Imagen = :NEW.PRO_Imagen, p.ACTIVO = :NEW.ACTIVO, p.UPDATED_AT = :NEW.UPDATED_AT WHERE p.PRO_Codigo = :OLD.PRO_Codigo;
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_productos_delete_repl
AFTER DELETE ON productos
FOR EACH ROW
BEGIN
    DELETE FROM productos@link_comee WHERE PRO_Codigo = :OLD.PRO_Codigo;
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/

PROMPT ✓ Triggers PRODUCTOS creados

-- ============================================================================
-- CLIENTES: PROD -> COMEE
-- ============================================================================

PROMPT Creando triggers para CLIENTES (PROD -> COMEE)...

CREATE OR REPLACE TRIGGER trg_clientes_insert_repl
AFTER INSERT ON clientes
FOR EACH ROW
BEGIN
    INSERT INTO clientes@link_comee (
        CLI_Ced_Ruc, CLI_Nombre, CLI_Telefono, CLI_Correo,
        usuario_aplicacion_id, CREATED_AT, UPDATED_AT
    ) VALUES (
        :NEW.CLI_Ced_Ruc, :NEW.CLI_Nombre, :NEW.CLI_Telefono, :NEW.CLI_Correo,
        :NEW.usuario_aplicacion_id, :NEW.CREATED_AT, :NEW.UPDATED_AT
    );
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN NULL;
    WHEN OTHERS THEN NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_clientes_update_repl
AFTER UPDATE ON clientes
FOR EACH ROW
BEGIN
    UPDATE clientes@link_comee c SET c.CLI_Nombre = :NEW.CLI_Nombre, c.CLI_Telefono = :NEW.CLI_Telefono, c.CLI_Correo = :NEW.CLI_Correo, c.usuario_aplicacion_id = :NEW.usuario_aplicacion_id, c.UPDATED_AT = :NEW.UPDATED_AT WHERE c.CLI_Ced_Ruc = :OLD.CLI_Ced_Ruc;
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_clientes_delete_repl
AFTER DELETE ON clientes
FOR EACH ROW
BEGIN
    DELETE FROM clientes@link_comee WHERE CLI_Ced_Ruc = :OLD.CLI_Ced_Ruc;
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/

PROMPT ✓ Triggers CLIENTES creados

-- ============================================================================
-- FACTURAS: PROD -> COMEE
-- ============================================================================

PROMPT Creando triggers para FACTURAS (PROD -> COMEE)...

CREATE OR REPLACE TRIGGER trg_facturas_insert_repl
AFTER INSERT ON facturas
FOR EACH ROW
BEGIN
    INSERT INTO facturas@link_comee (
        FAC_Codigo, CLI_Ced_Ruc, FAC_IVA, FAC_Subtotal,
        FAC_Total, FAC_Estado, CREATED_AT, UPDATED_AT
    ) VALUES (
        :NEW.FAC_Codigo, :NEW.CLI_Ced_Ruc, :NEW.FAC_IVA, :NEW.FAC_Subtotal,
        :NEW.FAC_Total, :NEW.FAC_Estado, :NEW.CREATED_AT, :NEW.UPDATED_AT
    );
EXCEPTION
    WHEN DUP_VAL_ON_INDEX THEN NULL;
    WHEN OTHERS THEN NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_facturas_update_repl
AFTER UPDATE ON facturas
FOR EACH ROW
BEGIN
    UPDATE facturas@link_comee f SET f.CLI_Ced_Ruc = :NEW.CLI_Ced_Ruc, f.FAC_IVA = :NEW.FAC_IVA, f.FAC_Subtotal = :NEW.FAC_Subtotal, f.FAC_Total = :NEW.FAC_Total, f.FAC_Estado = :NEW.FAC_Estado, f.UPDATED_AT = :NEW.UPDATED_AT WHERE f.FAC_Codigo = :OLD.FAC_Codigo;
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_facturas_delete_repl
AFTER DELETE ON facturas
FOR EACH ROW
BEGIN
    DELETE FROM facturas@link_comee WHERE FAC_Codigo = :OLD.FAC_Codigo;
EXCEPTION
    WHEN OTHERS THEN NULL;
END;
/

PROMPT ✓ Triggers FACTURAS creados

PROMPT
PROMPT ════════════════════════════════════════════════════════════════
PROMPT ✅ Todos los triggers creados en PROD
PROMPT ════════════════════════════════════════════════════════════════
PROMPT

EXIT;
