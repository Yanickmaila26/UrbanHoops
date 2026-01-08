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

        // El modelo ahora se encarga de decidir si filtra o no
        $productos = Producto::getProductos($search);

        return view('productos.index', compact('productos', 'search'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $ultimoProducto = Producto::orderBy('created_at', 'desc')->first();

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
    public function update(Request $request, Producto $producto)
    {
        // Pasamos el código actual para ignorar la regla 'unique' de PRO_Codigo
        $validator = Validator::make($request->all(), Producto::rules($producto->PRO_Codigo), Producto::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        if ($request->hasFile('PRO_Imagen')) {
            if ($producto->PRO_Imagen) {
                Storage::disk('public')->delete($producto->PRO_Imagen);
            }
            $data['PRO_Imagen'] = $request->file('PRO_Imagen')->store('productos', 'public');
        }

        $producto->updateProducto($data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar producto y su archivo de imagen.
     */

    public function destroy(Producto $producto)
    {
        try {
            // 1. Verificar si el producto tiene una imagen asignada
            if ($producto->PRO_Imagen) {
                // 2. Intentar borrar el archivo físico del disco 'public'
                if (Storage::disk('public')->exists($producto->PRO_Imagen)) {
                    Storage::disk('public')->delete($producto->PRO_Imagen);
                }
            }

            // 3. Eliminar el registro de la base de datos
            $producto->delete();

            return redirect()->route('products.index')
                ->with('success', 'Producto e imagen eliminados correctamente.');
        } catch (\Exception $e) {
            // En caso de error (ej. restricción de llave foránea o error de disco)
            return redirect()->route('products.index')
                ->with('error', 'No se pudo eliminar el producto: ' . $e->getMessage());
        }
    }
}
