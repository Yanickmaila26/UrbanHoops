<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Servicio para interactuar con procedimientos almacenados de Oracle
 */
class OracleStoredProcedureService
{
    /**
     * Finalizar compra web usando el procedimiento almacenado
     * 
     * @param string $carritoId ID del carrito a procesar
     * @return array ['success' => bool, 'factura_id' => string, 'message' => string]
     * @throws Exception
     */
    public function finalizarCompraWeb(string $carritoId): array
    {
        try {
            // Preparar variables de salida
            $facturaId = '';
            $success = 0;
            $message = '';

            // Ejecutar procedimiento almacenado
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('BEGIN sp_finalizar_compra_web(:carrito, :factura, :success, :message); END;');

            // Bind de parámetros
            $stmt->bindParam(':carrito', $carritoId, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':factura', $facturaId, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':success', $success, \PDO::PARAM_INT);
            $stmt->bindParam(':message', $message, \PDO::PARAM_STR, 500);

            // Ejecutar
            $stmt->execute();

            return [
                'success' => (bool) $success,
                'factura_id' => $facturaId,
                'message' => $message
            ];
        } catch (\PDOException $e) {
            throw new Exception('Error al ejecutar procedimiento almacenado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener historial de auditoría de un cliente
     * 
     * @param string $cedulaRuc Cédula/RUC del cliente
     * @param int $dias Días hacia atrás (por defecto 30)
     * @return array
     */
    public function obtenerAuditoriaCliente(string $cedulaRuc, int $dias = 30): array
    {
        return DB::select("
            SELECT 
                LOG_ID,
                LOG_CAMPO,
                LOG_VALOR_ANTERIOR,
                LOG_VALOR_NUEVO,
                LOG_FECHA,
                LOG_USUARIO
            FROM LOG_CLIENTES
            WHERE CLI_CED_RUC = :cedula
              AND LOG_FECHA >= SYSDATE - :dias
            ORDER BY LOG_FECHA DESC
        ", [
            'cedula' => $cedulaRuc,
            'dias' => $dias
        ]);
    }

    /**
     * Obtener todos los cambios de un campo específico
     * 
     * @param string $campo Nombre del campo (CLI_CORREO, CLI_DIRECCION, etc.)
     * @param int $dias Días hacia atrás (por defecto 30)
     * @return array
     */
    public function obtenerCambiosPorCampo(string $campo, int $dias = 30): array
    {
        return DB::select("
            SELECT 
                c.CLI_NOMBRE,
                c.CLI_CED_RUC,
                lc.LOG_VALOR_ANTERIOR,
                lc.LOG_VALOR_NUEVO,
                lc.LOG_FECHA,
                lc.LOG_USUARIO
            FROM LOG_CLIENTES lc
            JOIN CLIENTES c ON lc.CLI_CED_RUC = c.CLI_CED_RUC
            WHERE lc.LOG_CAMPO = :campo
              AND lc.LOG_FECHA >= SYSDATE - :dias
            ORDER BY lc.LOG_FECHA DESC
        ", [
            'campo' => $campo,
            'dias' => $dias
        ]);
    }

    /**
     * Registrar ingreso de mercadería a bodega desde orden de compra
     * 
     * @param string $ordenNumero Número de orden de compra
     * @param string $bodegaCodigo Código de la bodega destino
     * @return array ['success' => bool, 'message' => string]
     * @throws Exception
     */
    public function registrarIngresoBodega(string $ordenNumero, string $bodegaCodigo = 'BOD001'): array
    {
        try {
            $success = 0;
            $message = '';

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('BEGIN sp_registrar_ingreso_bodega(:orden, :bodega, :success, :message); END;');

            $stmt->bindParam(':orden', $ordenNumero, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':bodega', $bodegaCodigo, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':success', $success, \PDO::PARAM_INT);
            $stmt->bindParam(':message', $message, \PDO::PARAM_STR, 500);

            $stmt->execute();

            return [
                'success' => (bool) $success,
                'message' => $message
            ];
        } catch (\PDOException $e) {
            throw new Exception('Error al ejecutar procedimiento almacenado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener movimientos de kardex de un producto
     * 
     * @param string $productoCodigo Código del producto
     * @param string|null $bodegaCodigo Código de bodega (null = todas)
     * @param int $dias Días hacia atrás (por defecto 30)
     * @return array
     */
    public function obtenerKardexProducto(string $productoCodigo, ?string $bodegaCodigo = null, int $dias = 30): array
    {
        $sql = "
            SELECT 
                k.KAR_CODIGO,
                k.KAR_FECHA,
                k.KAR_TIPO_MOVIMIENTO,
                k.KAR_CANTIDAD_ENTRADA,
                k.KAR_CANTIDAD_SALIDA,
                k.KAR_SALDO_ANTERIOR,
                k.KAR_SALDO_ACTUAL,
                k.KAR_REFERENCIA,
                b.BOD_NOMBRE,
                p.PRO_NOMBRE
            FROM KARDEXES k
            JOIN BODEGAS b ON k.BOD_CODIGO = b.BOD_CODIGO
            JOIN PRODUCTOS p ON k.PRO_CODIGO = p.PRO_CODIGO
            WHERE k.PRO_CODIGO = :producto
        ";

        $params = ['producto' => $productoCodigo];

        if ($bodegaCodigo) {
            $sql .= " AND k.BOD_CODIGO = :bodega";
            $params['bodega'] = $bodegaCodigo;
        }

        if ($dias > 0) {
            $sql .= " AND k.KAR_FECHA >= SYSDATE - :dias";
            $params['dias'] = $dias;
        }

        $sql .= " ORDER BY k.KAR_FECHA DESC, k.KAR_CODIGO DESC";

        return DB::select($sql, $params);
    }

    /**
     * Emitir factura desde una orden (Web o POS)
     * 
     * @param string $tipoOrden 'WEB' o 'POS'
     * @param string $ordenId ID de la orden (carrito o pedido)
     * @return array ['success' => bool, 'factura_id' => string, 'message' => string]
     * @throws Exception
     */
    public function emitirFactura(string $tipoOrden, string $ordenId): array
    {
        try {
            $success = 0;
            $facturaId = '';
            $message = '';

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('BEGIN sp_emitir_factura(:tipo, :orden, :factura_id, :success, :message); END;');

            $stmt->bindParam(':tipo', $tipoOrden, \PDO::PARAM_STR, 10);
            $stmt->bindParam(':orden', $ordenId, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':factura_id', $facturaId, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':success', $success, \PDO::PARAM_INT);
            $stmt->bindParam(':message', $message, \PDO::PARAM_STR, 500);

            $stmt->execute();

            return [
                'success' => (bool) $success,
                'factura_id' => $facturaId,
                'message' => $message
            ];
        } catch (\PDOException $e) {
            throw new Exception('Error al ejecutar procedimiento almacenado: ' . $e->getMessage());
        }
    }
}
