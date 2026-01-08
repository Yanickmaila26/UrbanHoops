<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bodega extends Model
{
    protected $table = 'bodegas';
    protected $primaryKey = 'BOD_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'BOD_Codigo',
        'TRN_Codigo',
        'ORC_Numero',
        'PRO_Codigo',
        'BOD_cantidad'
    ];

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'TRN_Codigo');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Codigo');
    }
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'ORC_Numero');
    }

    public static function getMovimientos($search = null)
    {
        $query = self::with(['transaccion', 'producto', 'ordenCompra']);
        if ($search) {
            $query->where('BOD_Codigo', 'like', "%{$search}%")
                ->orWhere('ORC_Numero', 'like', "%{$search}%")
                ->orWhere('PRO_Codigo', 'like', "%{$search}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public static function crearMovimiento(array $data)
    {
        return DB::transaction(function () use ($data) {
            $trn = Transaccion::where('TRN_Codigo', $data['TRN_Codigo'])->firstOrFail();

            // CASO A: ORDEN DE COMPRA
            if (!empty($data['ORC_Numero'])) {
                $oc = OrdenCompra::with('productos')->where('ORC_Numero', $data['ORC_Numero'])->firstOrFail();

                foreach ($oc->productos as $prod) {
                    // Si es T07 (Cancelación), la cantidad para el registro es 0 y NO afecta stock
                    $esCancelacion = ($data['TRN_Codigo'] === 'T07');
                    $cantidadRegistro = $esCancelacion ? 0 : $prod->pivot->cantidad_solicitada;

                    if (!$esCancelacion) {
                        self::actualizarStockFisico($prod, $trn->TRN_Tipo, $prod->pivot->cantidad_solicitada);
                    }

                    // Crear registro en Bodega con cantidad 0 si es cancelado
                    self::create([
                        'BOD_Codigo'   => $data['BOD_Codigo'] . '-' . $prod->PRO_Codigo,
                        'TRN_Codigo'   => $data['TRN_Codigo'],
                        'ORC_Numero'   => $data['ORC_Numero'],
                        'PRO_Codigo'   => $prod->PRO_Codigo,
                        'BOD_cantidad' => $cantidadRegistro, // Aquí se asigna el 0
                    ]);
                }

                // Desactivar la orden para que no aparezca más en la lista de 'Activas'
                $oc->update(['ORC_Estado' => false]);

                return true;
            }

            // CASO B: AJUSTE MANUAL
            else if (!empty($data['PRO_Codigo'])) {
                $prod = Producto::where('PRO_Codigo', $data['PRO_Codigo'])->firstOrFail();
                self::actualizarStockFisico($prod, $trn->TRN_Tipo, $data['BOD_cantidad']);
                return self::create($data);
            }
        });
    }

    private static function actualizarStockFisico($producto, $tipo, $cantidad)
    {
        if ($tipo === 'E') {
            $producto->increment('PRO_Stock', $cantidad);
        } else {
            // Validar stock negativo (Tipo 'S')
            if ($producto->PRO_Stock < $cantidad) {
                throw new \Exception("Stock insuficiente para: {$producto->PRO_Nombre}. Disponible: {$producto->PRO_Stock}");
            }
            $producto->decrement('PRO_Stock', $cantidad);
        }
    }

    public static function rules()
    {
        return [
            'BOD_Codigo'   => 'required|string|max:25',
            'TRN_Codigo'   => 'required|exists:transaccions,TRN_Codigo',
            'ORC_Numero'   => 'nullable|exists:orden_compras,ORC_Numero',
            'PRO_Codigo'   => 'nullable|required_without:ORC_Numero|exists:productos,PRO_Codigo',
            'BOD_cantidad' => 'nullable|required_without:ORC_Numero|numeric|min:1',
        ];
    }

    public static function messages()
    {
        return [
            'BOD_Codigo.unique' => 'El código de movimiento ya existe.',
            'TRN_Codigo.required' => 'Debe seleccionar un tipo de transacción.',
            'ORC_Numero.required_without' => 'Debe seleccionar una Orden de Compra o un Producto.',
            'BOD_cantidad.required_if' => 'La cantidad es obligatoria para ajustes individuales.',
            'BOD_cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
