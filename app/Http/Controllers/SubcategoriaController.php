<?php

namespace App\Http\Controllers;

use App\Models\Subcategoria;
use App\Models\Categoria;
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $subcategorias = Subcategoria::with('categoria')
            ->when($search, function ($query, $search) {
                return $query->where('SCT_Nombre', 'like', "%{$search}%")
                    ->orWhere('SCT_Codigo', 'like', "%{$search}%")
                    ->orWhereHas('categoria', function ($q) use ($search) {
                        $q->where('CAT_Nombre', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('subcategorias.index', compact('subcategorias', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::all();
        return view('subcategorias.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'SCT_Nombre' => 'required|string|max:100',
            'CAT_Codigo' => 'required|exists:categorias,CAT_Codigo'
        ], [
            'SCT_Nombre.required' => 'El nombre de la subcategoría es obligatorio.',
            'CAT_Codigo.required' => 'Debe seleccionar una categoría.',
            'CAT_Codigo.exists' => 'La categoría seleccionada no es válida.'
        ]);

        Subcategoria::create($request->all());

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subcategoria $subcategoria)
    {
        return view('subcategorias.show', compact('subcategoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subcategoria $subcategoria)
    {
        $categorias = Categoria::all();
        return view('subcategorias.edit', compact('subcategoria', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subcategoria $subcategoria)
    {
        $request->validate([
            'SCT_Nombre' => 'required|string|max:100',
            'CAT_Codigo' => 'required|exists:categorias,CAT_Codigo'
        ], [
            'SCT_Nombre.required' => 'El nombre de la subcategoría es obligatorio.',
            'CAT_Codigo.required' => 'Debe seleccionar una categoría.'
        ]);

        $subcategoria->update($request->all());

        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subcategoria $subcategoria)
    {
        $subcategoria->delete();
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría eliminada correctamente.');
    }
}
