<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    /**
     * Listado de productos con búsqueda general.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Producto::activos();

        if ($search) {
            $query->search($search);
        }

        $productos = $query->orderBy('PRO_Codigo', 'desc')->paginate(10);
        return view('productos.index', compact('productos', 'search'));
    }

    public function create()
    {
        $ultimoProducto = Producto::orderBy('PRO_Codigo', 'desc')->first();
        $nuevoCodigo = $ultimoProducto ? 'P' . str_pad((int)substr($ultimoProducto->PRO_Codigo, 1) + 1, 3, '0', STR_PAD_LEFT) : 'P001';

        $proveedores = Proveedor::getProveedoresActivos();

        return view('productos.create', compact('nuevoCodigo', 'proveedores'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Producto::rules(), Producto::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('PRO_Imagen');

        if ($request->hasFile('PRO_Imagen')) {
            $data['PRO_Imagen'] = $request->file('PRO_Imagen')->store('productos', 'public');
        }

        try {
            Producto::createProducto($data);
            return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear producto: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $proveedores = Proveedor::getProveedoresActivos();
        return view('productos.edit', compact('producto', 'proveedores'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validator = Validator::make($request->all(), Producto::rules($producto->PRO_Codigo), Producto::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('PRO_Imagen');

        if ($request->hasFile('PRO_Imagen')) {
            if ($producto->PRO_Imagen) {
                Storage::disk('public')->delete($producto->PRO_Imagen);
            }
            $data['PRO_Imagen'] = $request->file('PRO_Imagen')->store('productos', 'public');
        }

        try {
            $producto->updateProducto($data);
            return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar producto: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Producto $producto)
    {
        try {
            Producto::deleteProducto($producto);
            return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }
}
