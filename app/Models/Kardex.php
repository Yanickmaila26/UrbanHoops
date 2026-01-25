<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kardex extends Model
{
    protected $table = 'kardexes';
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
                    $cantidad = $prod->pivot->cantidad_solicitada;
                    $talla = $prod->pivot->DOC_Talla;

                    if (!$esCancelacion) {
                        self::actualizarStockFisico($prod, $trn->TRN_Tipo, $cantidad, $data['BOD_Codigo'], $talla);
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
                return true;
            }

            // CASO B: FACTURA (SALIDA)
            else if (!empty($data['FAC_Codigo'])) {
                $factura = Factura::with('productos')->where('FAC_Codigo', $data['FAC_Codigo'])->firstOrFail();

                foreach ($factura->productos as $prod) {
                    $cantidad = $prod->pivot->DFC_Cantidad;
                    $talla = $prod->pivot->DFC_Talla;

                    self::actualizarStockFisico($prod, $trn->TRN_Tipo, $cantidad, $data['BOD_Codigo'], $talla);

                    self::create([
                        'KAR_Codigo'   => self::generateId(),
                        'BOD_Codigo'   => $data['BOD_Codigo'],
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
                $talla = $data['talla'] ?? null; // Assuming passed in data for manual adjustment
                self::actualizarStockFisico($prod, $trn->TRN_Tipo, $data['KAR_cantidad'], $data['BOD_Codigo'], $talla);

                $data['KAR_Codigo'] = self::generateId();
                return self::create($data);
            }
        });
    }

    private static function actualizarStockFisico($producto, $tipo, $cantidad, $bodegaCodigo, $talla = null)
    {
        // 1. Update Total Stock (Product Level)
        if ($tipo === 'E') {
            $producto->PRO_Stock += $cantidad;
        } else {
            if ($producto->PRO_Stock < $cantidad) {
                throw new \Exception("Stock insuficiente para: {$producto->PRO_Nombre}. Disponible: {$producto->PRO_Stock}");
            }
            $producto->PRO_Stock -= $cantidad;
        }

        // 2. Update Warehouse-Product Stock (producto_bodega.PXB_Stock)
        $pivotRecord = DB::table('producto_bodega')
            ->where('PRO_Codigo', $producto->PRO_Codigo)
            ->where('BOD_Codigo', $bodegaCodigo)
            ->first();

        if ($pivotRecord) {
            $newWarehouseStock = $pivotRecord->PXB_Stock;
            if ($tipo === 'E') {
                $newWarehouseStock += $cantidad;
            } else {
                if ($pivotRecord->PXB_Stock < $cantidad) {
                    throw new \Exception("Stock insuficiente en bodega para: {$producto->PRO_Nombre}. Disponible en bodega: {$pivotRecord->PXB_Stock}");
                }
                $newWarehouseStock -= $cantidad;
            }

            DB::table('producto_bodega')
                ->where('PRO_Codigo', $producto->PRO_Codigo)
                ->where('BOD_Codigo', $bodegaCodigo)
                ->update(['PXB_Stock' => $newWarehouseStock, 'updated_at' => now()]);
        } else {
            // If relationship doesn't exist, create it (Entry only)
            if ($tipo === 'E') {
                DB::table('producto_bodega')->insert([
                    'PRO_Codigo' => $producto->PRO_Codigo,
                    'BOD_Codigo' => $bodegaCodigo,
                    'PXB_Stock' => $cantidad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                throw new \Exception("El producto {$producto->PRO_Nombre} no está asignado a la bodega seleccionada.");
            }
        }

        // 3. Update JSON Size Stock if talla provided
        if ($talla) {
            $sizes = $producto->PRO_Talla; // Array access because cast
            if (is_array($sizes)) {
                $found = false;
                foreach ($sizes as &$s) {
                    if (isset($s['talla']) && $s['talla'] == $talla) {
                        if ($tipo === 'E') {
                            $s['stock'] = (int)($s['stock'] ?? 0) + $cantidad;
                        } else {
                            if ((int)($s['stock'] ?? 0) < $cantidad) {
                                throw new \Exception("Stock insuficiente para: {$producto->PRO_Nombre} Talla: {$talla}.");
                            }
                            $s['stock'] = (int)($s['stock'] ?? 0) - $cantidad;
                        }
                        $found = true;
                        break;
                    }
                }
                // If Entry and size not found, add it
                if (!$found && $tipo === 'E') {
                    $sizes[] = ['talla' => $talla, 'stock' => $cantidad];
                }
                // If Exit and size not found -> Error
                if (!$found && $tipo !== 'E') {
                    throw new \Exception("La talla {$talla} no existe para el producto {$producto->PRO_Nombre}.");
                }

                $producto->PRO_Talla = $sizes; // Reassign to trigger set/json encoding
            } elseif ($tipo === 'E') {
                // Was null/empty, init it
                $producto->PRO_Talla = [['talla' => $talla, 'stock' => $cantidad]];
            }
        }

        $producto->save();
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
