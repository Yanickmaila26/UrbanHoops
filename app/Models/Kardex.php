<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kardex extends Model
{
    protected $table = 'kardex';
    protected $primaryKey = 'KAR_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KAR_Codigo', // Added PK to fillable if needed, or rely on generation
        'BOD_Codigo',
        'TRN_Codigo',
        'ORC_Numero',
        'FAC_Codigo',
        'PRO_Codigo',
        'KAR_cantidad' // Migration says KAR_cantidad, Model said BOD_cantidad. Migration step 359 says KAR_cantidad. User model step 358 said BOD_cantidad. I will use KAR_cantidad to match migration.
    ];

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'TRN_Codigo', 'TRN_Codigo');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Codigo', 'PRO_Codigo');
    }
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'ORC_Numero', 'ORC_Numero');
    }
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'FAC_Codigo', 'FAC_Codigo');
    }

    public static function getMovimientos($search = null)
    {
        $query = self::with(['transaccion', 'producto', 'ordenCompra', 'factura']);
        if ($search) {
            $query->where('KAR_Codigo', 'like', "%{$search}%")
                ->orWhere('ORC_Numero', 'like', "%{$search}%")
                ->orWhere('FAC_Codigo', 'like', "%{$search}%")
                ->orWhereHas('transaccion', function ($q) use ($search) {
                    $q->where('TRN_Nombre', 'like', "%{$search}%");
                })
                ->orWhereHas('producto', function ($q) use ($search) {
                    $q->where('PRO_Nombre', 'like', "%{$search}%");
                })
                ->orWhere('PRO_Codigo', 'like', "%{$search}%");
        }
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public static function crearMovimiento(array $data)
    {
        return DB::transaction(function () use ($data) {
            $trn = Transaccion::where('TRN_Codigo', $data['TRN_Codigo'])->firstOrFail();

            // CASO A: ORDEN DE COMPRA (ENTRADA)
            if (!empty($data['ORC_Numero'])) {
                $oc = OrdenCompra::with('productos')->where('ORC_Numero', $data['ORC_Numero'])->firstOrFail();

                foreach ($oc->productos as $prod) {
                    $esCancelacion = ($data['TRN_Codigo'] === 'T07');
                    $cantidad = $prod->pivot->cantidad_solicitada; // Assuming 'cantidad_solicitada' is correct field name in pivot. Check OrdenCompra model if possible. Step 275 doesn't show pivot fields.
                    // Assuming pivot is correct based on user code.

                    if (!$esCancelacion) {
                        self::actualizarStockFisico($prod, $trn->TRN_Tipo, $cantidad);
                    }

                    self::create([
                        'KAR_Codigo'   => self::generateId(),
                        'BOD_Codigo'   => $data['BOD_Codigo'],
                        'TRN_Codigo'   => $data['TRN_Codigo'],
                        'ORC_Numero'   => $data['ORC_Numero'],
                        'PRO_Codigo'   => $prod->PRO_Codigo,
                        'KAR_cantidad' => $esCancelacion ? 0 : $cantidad,
                    ]);
                }

                // Update Order Status if needed
                if (!$esCancelacion) {
                    // $oc->update(['ORC_Estado' => 'Procesada']); // If status exists
                }
                return true;
            }

            // CASO B: FACTURA (SALIDA)
            else if (!empty($data['FAC_Codigo'])) {
                $factura = Factura::with('productos')->where('FAC_Codigo', $data['FAC_Codigo'])->firstOrFail();

                foreach ($factura->productos as $prod) {
                    $cantidad = $prod->pivot->DFC_Cantidad;
                    self::actualizarStockFisico($prod, $trn->TRN_Tipo, $cantidad);

                    self::create([
                        'KAR_Codigo'   => self::generateId(),
                        'BOD_Codigo'   => $data['BOD_Codigo'], // Usually picking a default bodega if not specified? Or passed in.
                        'TRN_Codigo'   => $data['TRN_Codigo'],
                        'FAC_Codigo'   => $data['FAC_Codigo'],
                        'PRO_Codigo'   => $prod->PRO_Codigo,
                        'KAR_cantidad' => $cantidad,
                    ]);
                }
                return true;
            }

            // CASO C: AJUSTE MANUAL
            else if (!empty($data['PRO_Codigo'])) {
                $prod = Producto::where('PRO_Codigo', $data['PRO_Codigo'])->firstOrFail();
                self::actualizarStockFisico($prod, $trn->TRN_Tipo, $data['KAR_cantidad']);

                $data['KAR_Codigo'] = self::generateId();
                return self::create($data);
            }
        });
    }

    private static function actualizarStockFisico($producto, $tipo, $cantidad)
    {
        if ($tipo === 'E') {
            $producto->increment('PRO_Stock', $cantidad);
        } else {
            if ($producto->PRO_Stock < $cantidad) {
                throw new \Exception("Stock insuficiente para: {$producto->PRO_Nombre}. Disponible: {$producto->PRO_Stock}");
            }
            $producto->decrement('PRO_Stock', $cantidad);
        }
    }

    public static function generateId()
    {
        $last = self::orderBy('KAR_Codigo', 'desc')->first();
        if (!$last) {
            return 'KAR00001';
        }
        $number = intval(substr($last->KAR_Codigo, 3)) + 1;
        return 'KAR' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function rules()
    {
        return [
            'BOD_Codigo'   => 'required|exists:bodegas,BOD_Codigo',
            'TRN_Codigo'   => 'required|exists:transaccions,TRN_Codigo',
            'ORC_Numero'   => 'nullable|exists:orden_compras,ORC_Numero',
            'FAC_Codigo'   => 'nullable|exists:facturas,FAC_Codigo',
            'PRO_Codigo'   => 'nullable|required_without_all:ORC_Numero,FAC_Codigo|exists:productos,PRO_Codigo',
            'KAR_cantidad' => 'nullable|required_without_all:ORC_Numero,FAC_Codigo|numeric|min:1',
        ];
    }

    public static function messages()
    {
        return [
            'BOD_Codigo.required' => 'La bodega es obligatoria.',
            'TRN_Codigo.required' => 'Debe seleccionar un tipo de transacción.',
            'ORC_Numero.required_without_all' => 'Debe seleccionar una Orden, Factura o Producto.',
            'KAR_cantidad.required_without_all' => 'La cantidad es obligatoria para ajustes manuales.',
        ];
    }
}
