<?php

namespace App\Http\Controllers;

use App\Models\Producto;
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

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $ultimoProducto = Producto::orderBy('PRO_Codigo', 'desc')->first();

        if (!$ultimoProducto) {
            $nuevoCodigo = 'P001';
        } else {
            // Extraer el número del código (asumiendo formato P001)
            $numeroUltimo = (int) substr($ultimoProducto->PRO_Codigo, 1);
            // Generar el nuevo con ceros a la izquierda
            $nuevoCodigo = 'P' . str_pad($numeroUltimo + 1, 3, '0', STR_PAD_LEFT);
        }
        return view('productos.create', compact('nuevoCodigo'));
    }

    /**
     * Guardar nuevo producto.
     */
    public function store(Request $request)
    {
        // Validar usando las reglas y mensajes del Modelo
        $validator = Validator::make($request->all(), Producto::rules(), Producto::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('PRO_Imagen')) {
            $ruta = $request->file('PRO_Imagen')->store('productos', 'public');
            $data['PRO_Imagen'] = $ruta;
        }

        Producto::createProducto($data);

        return redirect()->route('products.index')->with('success', 'Producto registrado exitosamente.');
    }

    /**
     * Mostrar un producto específico.
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualizar producto y gestionar reemplazo de imagen.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $validator = Validator::make($request->all(), Producto::rules($id), Producto::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            if ($request->hasFile('PRO_Imagen')) {
                if ($producto->PRO_Imagen && Storage::disk('public')->exists($producto->PRO_Imagen)) {
                    Storage::disk('public')->delete($producto->PRO_Imagen);
                }
                $data['PRO_Imagen'] = $request->file('PRO_Imagen')->store('productos', 'public');
            }

            $producto->updateProducto($data);

            return redirect()->route('products.index')->with('success', 'Producto actualizado.');
        } catch (\Exception $e) {
            // Log::error($e->getMessage()); // Buena práctica: loguear el error real internamente
            return redirect()->back()->with('error', 'Ocurrió un problema al actualizar el producto. Verifique los datos e intente nuevamente.');
        }
    }

    /**
     * Eliminar producto y su archivo de imagen.
     */

    public function destroy(Producto $producto)
    {
        $producto->update(['activo' => false]);
        return redirect()->route('products.index')->with('success', 'Producto eliminado (borrado lógico).');
    }
}
