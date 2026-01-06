<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener término de búsqueda general
        $search = $request->input('search');

        // Consulta base
        $query = Proveedor::query();

        // Aplicar filtro general si existe
        if ($search) {
            $query->generalSearch($search);
        }

        // Ordenar por creación (más reciente primero)
        $query->orderBy('created_at', 'desc');

        // Paginación
        $suppliers = $query->paginate(10)->withQueryString();

        return view('proveedores.index', compact('suppliers', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), Proveedor::rules(), Proveedor::messages());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Crear proveedor
            Proveedor::create($validator->validated());
            return redirect()->route('suppliers.index')
                ->with('success', 'Proveedor creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el proveedor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Proveedor::findOrFail($id);
        return view('proveedores.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Proveedor::findOrFail($id);

        // Validar datos
        $validator = Validator::make($request->all(), Proveedor::rules($id), Proveedor::messages());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Actualizar proveedor
            $supplier->update($validator->validated());

            return redirect()->route('suppliers.index')
                ->with('success', 'Proveedor actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Proveedor::findOrFail($id);

        try {
            $supplier->delete();

            return redirect()->route('suppliers.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }


    /**
     * Validate supplier data.
     */
    public function validateSupplier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'PRV_Ced_Ruc' => 'required|string|size:13|unique:proveedors,PRV_Ced_Ruc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors()
            ]);
        }

        return response()->json(['valid' => true]);
    }
}
