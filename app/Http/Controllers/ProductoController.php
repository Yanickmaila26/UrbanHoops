<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Listado de productos.
     */
    public function index(Request $request)
    {
        // Obtener término de búsqueda general
        $search = $request->input('search');

        // Consulta base
        $query = Producto::query();

        // Aplicar filtro general si existe
        if ($search) {
            $query->generalSearch($search);
        }

        // Ordenar por creación (más reciente primero)
        $query->orderBy('created_at', 'desc');

        // Paginación
        $productos = $query->paginate(10)->withQueryString();

        return view('productos.index', compact('productos', 'search'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Guardar nuevo producto con imagen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'PRO_Codigo'            => 'required|string|max:15|unique:productos',
            'PRO_Nombre'            => 'required|string|max:60',
            'PRO_Descripcion_Corta' => 'required|string|max:100',
            'PRO_Descripcion_Larga' => 'required|string',
            'PRO_Imagen'            => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
        ]);

        $data = $request->all();

        if ($request->hasFile('PRO_Imagen')) {

            $rutaRelativa = $request->file('PRO_Imagen')->store('productos', 'public');
            $rutaAbsoluta = storage_path('app/public/' . $rutaRelativa);
            dd($rutaAbsoluta);
        }

        \App\Models\Producto::create($data);

        return redirect()->route('products.index')->with('success', 'Producto creado con éxito.');
    }

    /**
     * Mostrar un producto específico.
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualizar producto y gestionar reemplazo de imagen.
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'PRO_Nombre'            => 'required|string|max:60',
            'PRO_Descripcion_Corta' => 'required|string|max:100',
            'PRO_Descripcion_Larga' => 'required|string',
            'PRO_Imagen'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('PRO_Imagen')) {
            if ($producto->PRO_Imagen) {
                Storage::disk('public')->delete($producto->PRO_Imagen);
            }
            // Guardar la nueva
            $data['PRO_Imagen'] = $request->file('PRO_Imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Eliminar producto y su archivo de imagen.
     */
    public function destroy(Producto $producto)
    {
        if ($producto->PRO_Imagen) {
            Storage::disk('public')->delete($producto->PRO_Imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
