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

        // Category simulation via Search
        if ($request->filled('category')) {
            $category = $request->category;
            $query->where(function ($q) use ($category) {
                $q->where('PRO_Nombre', 'like', "%{$category}%")
                    ->orWhere('PRO_Descripcion', 'like', "%{$category}%")
                    ->orWhere('PRO_Marca', 'like', "%{$category}%");
            });
        }

        $productos = $query->paginate(12)->withQueryString();
        $maxPrice = Producto::max('PRO_Precio') ?? 300;

        return view('productos_servicios', compact('productos', 'maxPrice'));
    }

    public function show($producto)
    {
        $producto = Producto::where('PRO_Codigo', $producto)->where('activo', true)->firstOrFail();
        return view('detalle_producto', compact('producto'));
    }
}
