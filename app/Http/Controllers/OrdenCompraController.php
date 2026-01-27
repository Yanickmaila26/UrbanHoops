<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;

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
        $orden = OrdenCompra::with('proveedor')->findOrFail($id);

        // Use helper method to get products with details via raw queries
        $orden->productos = $orden->getProductosWithDetails();

        return view('ordenes.show', compact('orden'));
    }

    public function create()
    {
        // Lógica de código automático
        $ultima = OrdenCompra::orderBy('ORC_Numero', 'desc')->first();
        $nuevoCodigo = $ultima
            ? 'ORC' . str_pad((int)substr($ultima->ORC_Numero, 3) + 1, 3, '0', STR_PAD_LEFT)
            : 'ORC001';

        $proveedores = Proveedor::all();
        $productosRaw = Producto::all();

        // Normalize data for Frontend execution to avoid Oracle Case Sensitivity issues in JSON
        $productos = $productosRaw->map(function ($prod) {
            return [
                'PRO_Codigo' => $prod->PRO_Codigo ?? $prod->pro_codigo ?? $prod->PRO_CODIGO,
                'PRO_Nombre' => $prod->PRO_Nombre ?? $prod->pro_nombre ?? $prod->PRO_NOMBRE,
                'PRO_Precio' => $prod->PRO_Precio ?? $prod->pro_precio ?? $prod->PRO_PRECIO,
                'PRO_Talla'  => $prod->PRO_Talla  ?? $prod->pro_talla  ?? $prod->PRO_TALLA,
            ];
        });

        return view('ordenes.create', compact('nuevoCodigo', 'proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        // Removed Unique Product check as we allow same product with different sizes
        // But we should check exact duplicates (Same Product + Same Talla) - handled by client side but good to check here?
        // Let's rely on validation or logic.

        $request->validate(OrdenCompra::rules(), OrdenCompra::messages());

        // Custom Transaction
        $orden = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, &$orden) {
            $orden = OrdenCompra::create($request->all());

            foreach ($request->productos as $index => $proCodigo) {
                $orden->productos()->attach($proCodigo, [
                    'cantidad_solicitada' => $request->cantidades[$index],
                    'DOC_Talla' => $request->tallas[$index] ?? null
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Orden de Compra generada.');
    }

    public function edit(string $id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->productos = $orden->getProductosWithDetails();

        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('ordenes.edit', compact('orden', 'proveedores', 'productos'));
    }

    public function update(Request $request, string $id)
    {
        $orden = OrdenCompra::findOrFail($id);

        try {
            // 1. Actualizar cabecera
            $validator = Validator::make($request->all(), OrdenCompra::rules($id), OrdenCompra::messages());

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $orden->update($request->all());

            // 2. Sincronizar productos (Detach all, then Attach new)
            // sync() doesn't support duplicate IDs with different pivot values easily in one go with keyed array
            $orden->productos()->detach();

            foreach ($request->productos as $key => $prod_codigo) {
                $orden->productos()->attach($prod_codigo, [
                    'cantidad_solicitada' => $request->cantidades[$key],
                    'DOC_Talla' => $request->tallas[$key] ?? null
                ]);
            }

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
