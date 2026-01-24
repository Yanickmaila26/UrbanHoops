<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function catalogo(Request $request)
    {
        $query = Producto::query()->where('activo', true);

        // Price Filter
        if ($request->filled('max_price')) {
            $query->where('PRO_Precio', '<=', $request->max_price);
        }

        // Category Filter (by ID) - Products in any subcategory of this category
        if ($request->filled('category_id')) {
            $query->whereHas('subcategoria', function ($q) use ($request) {
                $q->where('CAT_Codigo', $request->category_id);
            });
        }

        // Subcategory Filter (by ID) - Specific subcategory
        if ($request->filled('subcategory_id')) {
            $query->where('SCT_Codigo', $request->subcategory_id);
        }

        // Text Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('PRO_Nombre', 'like', "%{$search}%")
                    ->orWhere('PRO_Descripcion', 'like', "%{$search}%")
                    ->orWhere('PRO_Marca', 'like', "%{$search}%");
            });
        }

        $productos = $query->paginate(12)->withQueryString();
        $maxPrice = Producto::max('PRO_Precio') ?? 300;

        // Fetch categories with sub-categories for the sidebar
        $categorias = \App\Models\Categoria::with('subcategorias')->get();

        return view('productos_servicios', compact('productos', 'maxPrice', 'categorias'));
    }

    public function show($producto)
    {
        $producto = Producto::where('PRO_Codigo', $producto)->where('activo', true)->firstOrFail();
        return view('detalle_producto', compact('producto'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'asunto' => 'required|in:consulta_producto,problema_pedido,envios,devolucion,sugerencia,otro',
            'mensaje' => 'required|string|min:10|max:1000',
            'aceptacion' => 'accepted',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Proporciona un correo electrónico válido.',
            'asunto.required' => 'Debes seleccionar un asunto.',
            'mensaje.required' => 'El mensaje no puede estar vacío.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'aceptacion.accepted' => 'Debes aceptar los términos y condiciones.',
        ]);

        // Here you would typically send an email, save to DB, etc.
        // For now, we simulate a delay and return success.

        // sleep(1); // Optional: simulate network delay if desired, but removed for production speed

        return back()->with('success', '¡Gracias por contactarnos! Hemos recibido tu mensaje correctamente.');
    }
}
