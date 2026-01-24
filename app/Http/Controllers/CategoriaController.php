<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categorias = Categoria::when($search, function ($query, $search) {
            return $query->where('CAT_Nombre', 'like', "%{$search}%")
                ->orWhere('CAT_Codigo', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('categorias.index', compact('categorias', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'CAT_Nombre' => 'required|string|max:100|unique:categorias,CAT_Nombre'
        ], [
            'CAT_Nombre.required' => 'El nombre de la categoría es obligatorio.',
            'CAT_Nombre.unique' => 'Esta categoría ya existe.'
        ]);

        Categoria::create($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'CAT_Nombre' => 'required|string|max:100|unique:categorias,CAT_Nombre,' . $categoria->CAT_Codigo . ',CAT_Codigo'
        ], [
            'CAT_Nombre.required' => 'El nombre de la categoría es obligatorio.',
            'CAT_Nombre.unique' => 'Esta categoría ya existe.'
        ]);

        $categoria->update($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        // Add check if has subcategories or products logic if needed, but onDelete cascade handled in DB for subcategories
        // However, Products have Set Null, so it's safeish. But deleting Category deletes Subcategories which sets Product SCT to Null.

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
