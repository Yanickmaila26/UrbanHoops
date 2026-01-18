<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Producto;
use App\Models\Transaccion;
use App\Models\OrdenCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BodegaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $bodegas = Bodega::when($search, function ($query, $search) {
            return $query->where('BOD_Nombre', 'like', "%{$search}%")
                ->orWhere('BOD_Codigo', 'like', "%{$search}%")
                ->orWhere('BOD_Ciudad', 'like', "%{$search}%");
        })->paginate(10);

        return view('bodegas.index', compact('bodegas', 'search'));
    }

    public function create()
    {
        return view('bodegas.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Bodega::rules(), Bodega::messages());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Bodega::create($request->all());
            return redirect()->route('warehouse.index')->with('success', 'Bodega creada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear bodega: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $bodega = Bodega::findOrFail($id);
        return view('bodegas.show', compact('bodega'));
    }

    public function edit($id)
    {
        $bodega = Bodega::findOrFail($id);
        return view('bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, $id)
    {
        $bodega = Bodega::findOrFail($id);
        $validator = Validator::make($request->all(), Bodega::rules($id), Bodega::messages());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $bodega->update($request->all());
            return redirect()->route('warehouse.index')->with('success', 'Bodega actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar bodega: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $bodega = Bodega::findOrFail($id);
            $bodega->delete();
            return redirect()->route('warehouse.index')->with('success', 'Bodega eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar la bodega porque tiene registros asociados.');
        }
    }
}
