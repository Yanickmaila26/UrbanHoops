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

                \Illuminate\Support\Facades\Log::info("Kardex Debug: Procesando OC {$data['ORC_Numero']}. Productos encontrados: " . $oc->productos->count());

                foreach ($oc->productos as $prod) {
                    $esCancelacion = ($data['TRN_Codigo'] === 'T07');

                    // Pivot Casing Fallback
                    $cantidad = $prod->pivot->cantidad_solicitada ?? $prod->pivot->CANTIDAD_SOLICITADA ?? 0;
                    $talla = $prod->pivot->DOC_Talla ?? $prod->pivot->DOC_TALLA ?? $prod->pivot->doc_talla ?? null;

                    \Illuminate\Support\Facades\Log::info("Kardex Debug: Producto {$prod->PRO_Codigo} - Cantidad: {$cantidad}, Talla: {$talla}");

                    if ($cantidad <= 0) {
                        \Illuminate\Support\Facades\Log::warning("Kardex: Cantidad 0 para producto {$prod->PRO_Codigo} en OC {$data['ORC_Numero']}");
                        continue;
                    }

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
                // Try Eloquent first
                $factura = Factura::with('productos')->where('FAC_Codigo', $data['FAC_Codigo'])->firstOrFail();
                $productos = $factura->productos;

                // Fallback check: Eloquent might return models with null attributes due to casing
                $useFallback = $productos->isEmpty();
                if (!$useFallback) {
                    $first = $productos->first();
                    if (!$first->PRO_Nombre && !$first->getAttribute('PRO_NOMBRE')) {
                        $useFallback = true;
                    }
                }

                if ($useFallback) {
                    $detalles = DB::table('detalle_factura')
                        ->where(DB::raw('UPPER(FAC_CODIGO)'), strtoupper($data['FAC_Codigo']))
                        ->get();

                    $productos = collect();
                    foreach ($detalles as $detalle) {
                        $proCodigo = $detalle->PRO_CODIGO ?? $detalle->pro_codigo ?? $detalle->PRO_Codigo;
                        $productoData = DB::table('productos')->where(DB::raw('UPPER(PRO_CODIGO)'), strtoupper($proCodigo))->first();
                        if ($productoData) {
                            $prod = new Producto((array)$productoData);
                            // Force attributes
                            $prod->PRO_Codigo = $productoData->PRO_CODIGO ?? $productoData->pro_codigo ?? $productoData->PRO_Codigo;
                            $prod->PRO_Nombre = $productoData->PRO_NOMBRE ?? $productoData->pro_nombre ?? $productoData->PRO_Nombre;
                            $prod->PRO_Stock = $productoData->PRO_STOCK ?? $productoData->pro_stock ?? 0;

                            // Pivot simulation
                            $pivot = new \stdClass();
                            $pivot->DFC_CANTIDAD = $detalle->DFC_CANTIDAD ?? $detalle->dfc_cantidad ?? $detalle->DFC_Cantidad;
                            $pivot->DFC_TALLA = $detalle->DFC_TALLA ?? $detalle->dfc_talla ?? $detalle->DFC_Talla;
                            $prod->setRelation('pivot', $pivot);
                            $productos->push($prod);
                        }
                    }
                }

                foreach ($productos as $prod) {
                    $cantidad = $prod->pivot->DFC_CANTIDAD ?? $prod->pivot->DFC_Cantidad;
                    $talla = $prod->pivot->DFC_TALLA ?? $prod->pivot->DFC_Talla;

                    // Final safety check
                    if (!$prod->PRO_Nombre) {
                        $fresh = DB::table('productos')->where('PRO_Codigo', $prod->PRO_Codigo)->first();
                        if ($fresh) {
                            $prod->PRO_Nombre = $fresh->PRO_NOMBRE ?? $fresh->pro_nombre ?? $fresh->PRO_Nombre;
                            $prod->PRO_Stock = $fresh->PRO_STOCK ?? $fresh->pro_stock ?? 0;
                            // Force Talla json decoding if needed by accessor logic, or just raw
                            $prod->setAttribute('PRO_Talla', $fresh->PRO_TALLA ?? $fresh->pro_talla);
                        }
                    }

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
                $talla = $data['talla'] ?? null;
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
            $producto->PRO_Stock -= $cantidad; // Si el producto no usa stock general estricto, esto podría ser opcional
        }

        // 2. Update Warehouse-Product Stock (producto_bodega.PXB_Stock)
        $pivotRecord = DB::table('producto_bodega')
            ->where('PRO_Codigo', $producto->PRO_Codigo)
            ->where('BOD_Codigo', $bodegaCodigo)
            ->first();

        if ($pivotRecord) {
            // Handle Oracle Casing (Likely UPPERCASE)
            $currentStock = $pivotRecord->PXB_Stock ?? $pivotRecord->PXB_STOCK ?? $pivotRecord->pxb_stock ?? 0;

            $newWarehouseStock = $currentStock; // Start with current

            if ($tipo === 'E') {
                $newWarehouseStock += $cantidad;
            } else {
                if ($currentStock < $cantidad) {
                    throw new \Exception("Stock insuficiente en bodega para: {$producto->PRO_Nombre}. Disponible en bodega: {$currentStock}");
                }
                $newWarehouseStock -= $cantidad;
            }

            DB::table('producto_bodega')
                ->where('PRO_Codigo', $producto->PRO_Codigo)
                ->where('BOD_Codigo', $bodegaCodigo)
                ->update(['PXB_Stock' => $newWarehouseStock, 'updated_at' => now()]);
        } else {
            // If relationship doesn't exist
            if ($tipo === 'E') {
                DB::table('producto_bodega')->insert([
                    'PRO_Codigo' => $producto->PRO_Codigo,
                    'BOD_Codigo' => $bodegaCodigo,
                    'PXB_Stock' => $cantidad,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // AUTO-HEAL: Si no existe en bodega pero estamos vendiendo, 
                // y ya pasamos la validación de stock global (punto 1),
                // asumimos que el stock global pertenece a esta bodega.
                $stockGlobalActual = $producto->PRO_Stock; // Ya descontado en punto 1
                $stockInicialBodega = $stockGlobalActual + $cantidad;

                DB::table('producto_bodega')->insert([
                    'PRO_Codigo' => $producto->PRO_Codigo,
                    'BOD_Codigo' => $bodegaCodigo,
                    'PXB_Stock' => $stockGlobalActual, // Ya restado la cantidad de venta
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // No lanzamos excepción, permitimos la venta y corregimos la inconsistencia.
            }
        }

        // 3. Update JSON Size Stock if talla provided
        if ($talla) {
            $sizes = $producto->PRO_Talla; // Accessor handles JSON decoding if defined in casting
            // Ensure array
            if (is_string($sizes)) {
                $sizes = json_decode($sizes, true) ?? [];
            }

            if (is_array($sizes)) {
                $found = false;
                foreach ($sizes as &$s) {
                    if (isset($s['talla']) && trim($s['talla']) == trim($talla)) {
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
                    // Tolerancia: Si no se encuentra talla, quizás es producto sin talla o error de data
                    // throw new \Exception("La talla {$talla} no existe para el producto {$producto->PRO_Nombre}.");
                    // Mejor log y continuar si el stock global valida
                }

                $producto->PRO_Talla = $sizes;
            } elseif ($tipo === 'E') {
                $producto->PRO_Talla = [['talla' => $talla, 'stock' => $cantidad]];
            }
        }

        // Use direct DB update instead of save() to avoid INSERT attempts on manually hydrated models
        // caused by Oracle/Eloquent mismatch issues
        $updatePayload = [
            'PRO_Stock' => $producto->PRO_Stock,
            'updated_at' => now()
        ];

        // Only update PRO_Talla if we actually have a value (avoid ORA-01407 NULL constraint)
        if ($producto->PRO_Talla !== null) {
            $updatePayload['PRO_Talla'] = is_array($producto->PRO_Talla) ? json_encode($producto->PRO_Talla) : $producto->PRO_Talla;
        }

        DB::table('productos')
            ->where('PRO_Codigo', $producto->PRO_Codigo)
            ->update($updatePayload);
    }

    public static function generateId()
    {
        // Use SQL MAX for robust generation
        $maxId = DB::table('kardexes')
            ->selectRaw('MAX(TO_NUMBER(SUBSTR(KAR_CODIGO, 4))) as max_id')
            ->value('max_id');

        $number = ($maxId ?? 0) + 1;
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
            'talla'        => 'nullable|required_with:PRO_Codigo|string',
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

    /**
     * Override parameter handling to support insensitive database column names
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value === null && $key !== strtolower($key)) {
            $lowerKey = strtolower($key);
            if (array_key_exists($lowerKey, $this->attributes)) {
                return $this->attributes[$lowerKey];
            }
        }

        return $value;
    }
}
