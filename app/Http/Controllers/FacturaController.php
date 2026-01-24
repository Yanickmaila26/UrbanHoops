<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Kardex;
use App\Models\Bodega;
use App\Models\Carrito;
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
        $productos = Producto::all();

        return view('facturas.create', compact('nuevoCodigo', 'clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate(Factura::rules(), Factura::messages());

        if (collect($request->productos)->unique()->count() !== count($request->productos)) {
            return back()->with('error', 'No se permiten productos duplicados en la factura.')->withInput();
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

                    $detalles[$codigo] = [
                        'DFC_Cantidad' => $cantidad,
                        'DFC_Precio' => $precio,
                        'DFC_Talla' => $request->tallas[$index] ?? null
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

                // 4. Registrar en Kardex y Descontar Stock
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

                // 5. Eliminar Carrito si existe (Opcional, pero lógico)
                Carrito::where('CLI_Ced_Ruc', $request->CLI_Ced_Ruc)->delete();
            });

            return redirect()->route('invoices.index')->with('success', 'Factura generada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar factura: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $factura = Factura::with(['cliente', 'productos'])->findOrFail($id);
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
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }
}
