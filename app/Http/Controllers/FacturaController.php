<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Kardex;
use App\Models\Bodega;
use App\Models\Carrito;
use App\Models\Pedido;
use App\Models\DatosFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $facturas = Factura::with('cliente')
            ->when($search, function ($query, $search) {
                return $query->where('FAC_Codigo', 'like', "%{$search}%")
                    ->orWhere('CLI_Ced_Ruc', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('CLI_Nombre', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('facturas.index', compact('facturas', 'search'));
    }

    public function create()
    {
        $nuevoCodigo = Factura::generateId();
        $clientes = Cliente::all();
        $productosRaw = Producto::all();

        // Normalize product data for JavaScript to avoid Oracle casing issues
        $productos = $productosRaw->map(function ($prod) {
            return [
                'PRO_Codigo' => $prod->PRO_Codigo ?? $prod->pro_codigo ?? $prod->PRO_CODIGO,
                'PRO_Nombre' => $prod->PRO_Nombre ?? $prod->pro_nombre ?? $prod->PRO_NOMBRE,
                'PRO_Precio' => $prod->PRO_Precio ?? $prod->pro_precio ?? $prod->PRO_PRECIO,
                'PRO_Talla'  => $prod->PRO_Talla  ?? $prod->pro_talla  ?? $prod->PRO_TALLA,
            ];
        });

        return view('facturas.create', compact('nuevoCodigo', 'clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate(array_merge(Factura::rules(), [
            'tallas' => ['required', 'array', 'min:1'],
        ]), Factura::messages());

        if (collect($request->productos)->count() !== count($request->tallas)) {
            return back()->with('error', 'Inconsistencia en los datos de productos y tallas.')->withInput();
        }

        // Duplicate check (Product + Size)
        $combinations = [];
        foreach ($request->productos as $index => $codigo) {
            $talla = $request->tallas[$index] ?? '';
            $key = $codigo . '_' . $talla;
            if (isset($combinations[$key])) {
                return back()->with('error', "El producto {$codigo} con talla {$talla} está duplicado en la factura.")->withInput();
            }
            $combinations[$key] = true;
        }

        try {
            DB::transaction(function () use ($request) {
                // 1. Calcular Total y Validar Precios
                $totalCallback = 0;
                $detalles = [];

                foreach ($request->productos as $index => $codigo) {
                    $producto = Producto::where('PRO_Codigo', $codigo)->firstOrFail();
                    $cantidad = $request->cantidades[$index];
                    $precio = $producto->PRO_Precio; // Secure fetch from DB

                    $subtotal = $precio * $cantidad;
                    $totalCallback += $subtotal;

                    // --- VALIDACIÓN DE STOCK MULTI-NIVEL ---
                    // 1. Validar Stock Global
                    if ($producto->PRO_Stock < $cantidad) {
                        throw new \Exception("Stock global insuficiente para {$producto->PRO_Nombre}. Disponible: {$producto->PRO_Stock}");
                    }

                    // 2. Validar Stock por Bodega (Usamos la primera bodega seleccionada en el sistema)
                    $bodega = Bodega::first();
                    if ($bodega) {
                        $stockBodega = DB::table('producto_bodega')
                            ->where('PRO_Codigo', $codigo)
                            ->where('BOD_Codigo', $bodega->BOD_Codigo)
                            ->value('PXB_Stock') ?? 0;

                        if ($stockBodega < $cantidad) {
                            throw new \Exception("Stock insuficiente en bodega ({$bodega->BOD_Nombre}) para {$producto->PRO_Nombre}. Disponible: {$stockBodega}");
                        }
                    }

                    // 3. Validar Stock por Talla
                    $tallaSolicitada = $request->tallas[$index] ?? null;
                    if ($tallaSolicitada) {
                        $tallas = is_string($producto->PRO_Talla) ? json_decode($producto->PRO_Talla, true) : $producto->PRO_Talla;
                        $tallaEncontrada = false;
                        foreach ($tallas as $t) {
                            if (trim($t['talla']) == trim($tallaSolicitada)) {
                                if ($t['stock'] < $cantidad) {
                                    throw new \Exception("Stock insuficiente para {$producto->PRO_Nombre} en talla {$tallaSolicitada}. Disponible: {$t['stock']}");
                                }
                                $tallaEncontrada = true;
                                break;
                            }
                        }
                        if (!$tallaEncontrada) {
                            throw new \Exception("La talla {$tallaSolicitada} no está registrada para {$producto->PRO_Nombre}");
                        }
                    }

                    $detalles[$codigo] = [
                        'DFC_Cantidad' => $cantidad,
                        'DFC_Precio' => $precio,
                        'DFC_Talla' => $tallaSolicitada
                    ];
                }

                // 2. Crear Factura
                $subtotal = $totalCallback;
                $ivaRate = config('urbanhoops.iva', 15);
                $totalValues = $subtotal * (1 + ($ivaRate / 100));

                $factura = Factura::create([
                    'FAC_Codigo' => $request->FAC_Codigo,
                    'CLI_Ced_Ruc' => $request->CLI_Ced_Ruc,
                    'FAC_Subtotal' => $subtotal,
                    'FAC_IVA' => $ivaRate,
                    'FAC_Total' => $totalValues,
                    'FAC_Estado' => 'Pen'
                ]);

                // 3. Attach Productos
                $factura->productos()->attach($detalles);

                // 4. Crear Datos de Facturación (Perfil para venta en tienda / Manual)
                $datosFacturacion = DatosFacturacion::where('DAF_CLI_Codigo', $request->CLI_Ced_Ruc)->first();
                if (!$datosFacturacion) {
                    $datosFacturacion = DatosFacturacion::create([
                        'DAF_CLI_Codigo' => $request->CLI_Ced_Ruc,
                        'DAF_Direccion' => $factura->cliente->CLI_Direccion ?? 'Venta Directa',
                        'DAF_Ciudad' => 'Quito',
                        'DAF_Estado' => 'Pichincha',
                        'DAF_CP' => '170150',
                        'DAF_Tarjeta_Numero' => '0000000000000000',
                        'DAF_Tarjeta_Expiracion' => '00/00',
                        'DAF_Tarjeta_CVV' => '000',
                    ]);
                }

                // 5. Crear Pedido (Vínculo necesario para el historial y seguimiento)
                Pedido::create([
                    'PED_CLI_Codigo' => $request->CLI_Ced_Ruc,
                    'PED_DAF_Codigo' => $datosFacturacion->DAF_Codigo,
                    'PED_FAC_Codigo' => $factura->FAC_Codigo,
                    'PED_Fecha' => now(),
                    'PED_Estado' => 'Entregado'
                ]);

                // 6. Registrar en Kardex y Descontar Stock
                // Usamos la primera bodega por defecto por ahora
                $bodega = Bodega::first();
                if ($bodega) {
                    Kardex::crearMovimiento([
                        'BOD_Codigo' => $bodega->BOD_Codigo,
                        'TRN_Codigo' => 'T04', // Venta
                        'FAC_Codigo' => $factura->FAC_Codigo,
                        // PRO_Codigo y Cantidad se manejan dentro de crearMovimiento iterando la factura
                    ]);
                }

                // 7. Eliminar Carrito si existe (Opcional, pero lógico)
                Carrito::where('CLI_Ced_Ruc', $request->CLI_Ced_Ruc)->delete();
            });

            return redirect()->route('invoices.index')->with('success', 'Factura generada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar factura: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $factura = Factura::with('cliente')->findOrFail($id);
        $factura->productos = $factura->getProductosWithDetails();
        return view('facturas.show', compact('factura'));
    }

    public function edit($id)
    {
        return redirect()->route('invoices.show', $id);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('invoices.show', $id);
    }

    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);

        if ($factura->FAC_Estado == 'Anu') {
            return back()->with('error', 'La factura ya está anulada.');
        }

        $factura->update(['FAC_Estado' => 'Anu']);
        return redirect()->route('invoices.index')->with('success', 'Factura anulada correctamente.');
    }
    public function getCart($dni)
    {
        $carrito = Carrito::where('CLI_Ced_Ruc', $dni)->with('productos')->first();

        if (!$carrito) {
            return response()->json(['success' => false, 'message' => 'Carrito no encontrado']);
        }

        $items = $carrito->productos->map(function ($producto) {
            return [
                'id' => $producto->PRO_Codigo,
                'name' => $producto->PRO_Nombre,
                'qty' => $producto->pivot->CRD_Cantidad,
                'price' => $producto->PRO_Precio,
                'talla' => $producto->pivot->CRD_Talla,
                'PRO_Talla' => $producto->PRO_Talla, // Include sizing JSON for frontend initialization
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }
}
