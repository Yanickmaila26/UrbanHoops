<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Producto;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $ordenes = OrdenCompra::getOrdenes($search);
        return view('ordenes.index', compact('ordenes', 'search'));
    }

    public function show(string $id)
    {
        $orden = OrdenCompra::with(['proveedor', 'productos'])->findOrFail($id);

        return view('ordenes.show', compact('orden'));
    }

    public function create()
    {
        // Lógica de código automático
        $ultima = OrdenCompra::orderBy('created_at', 'desc')->first();
        $nuevoCodigo = $ultima
            ? 'ORC' . str_pad((int)substr($ultima->ORC_Numero, 3) + 1, 3, '0', STR_PAD_LEFT)
            : 'ORC001';

        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('ordenes.create', compact('nuevoCodigo', 'proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $productosEnviados = $request->productos;

        if (count($productosEnviados) !== count(array_unique($productosEnviados))) {
            return redirect()->back()
                ->with('error', 'No se pueden enviar productos duplicados en la misma orden.')
                ->withInput();
        }

        $request->validate([
            'ORC_Numero' => 'required|unique:orden_compras',
            'PRV_Ced_Ruc' => 'required|exists:proveedors,PRV_Ced_Ruc',
            'productos' => 'required|array',
            'cantidades' => 'required|array',
        ]);

        $detalles = [];
        foreach ($request->productos as $index => $proCodigo) {
            $detalles[$proCodigo] = ['cantidad_solicitada' => $request->cantidades[$index]];
        }

        OrdenCompra::createOrden($request->all(), $detalles);

        return redirect()->route('purchase-orders.index')->with('success', 'Orden de Compra generada.');
    }

    public function edit(string $id)
    {
        $orden = OrdenCompra::with('productos')->findOrFail($id);
        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('ordenes.edit', compact('orden', 'proveedores', 'productos'));
    }

    public function update(Request $request, string $id)
    {
        $orden = OrdenCompra::findOrFail($id);

        // Validación de duplicados en el servidor
        if (count($request->productos) !== count(array_unique($request->productos))) {
            return redirect()->back()->with('error', 'No se permiten productos duplicados.')->withInput();
        }

        try {
            // 1. Actualizar cabecera (excepto el número que es único)
            $orden->update([
                'PRV_Ced_Ruc' => $request->PRV_Ced_Ruc,
                'ORC_Fecha_Emision' => $request->ORC_Fecha_Emision,
                'ORC_Fecha_Entrega' => $request->ORC_Fecha_Entrega,
                'ORC_Monto_Total'   => $request->ORC_Monto_Total,
            ]);

            // 2. Sincronizar productos (Borra los anteriores y crea los nuevos en la tabla pivot)
            $detalles = [];
            foreach ($request->productos as $key => $prod_codigo) {
                $detalles[$prod_codigo] = [
                    'cantidad_solicitada' => $request->cantidades[$key]
                ];
            }

            $orden->productos()->sync($detalles);

            return redirect()->route('purchase-orders.index')->with('success', 'Orden actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $orden = OrdenCompra::findOrFail($id);

        try {
            $orden->delete();
            return redirect()->route('purchase-orders.index')->with('success', 'Orden eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('purchase-orders.index')->with('error', 'No se pudo eliminar la orden.');
        }
    }
}
