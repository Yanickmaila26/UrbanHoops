<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedorController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Proveedor::activos();
        if ($search) {
            $query->search($search);
        }
        $suppliers = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('proveedores.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Proveedor::rules(), Proveedor::messages());
        if ($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();

        Proveedor::createProveedor($validator->validated());

        return redirect()->route('suppliers.index')->with('success', 'Proveedor creado.');
    }

    public function show(string $id)
    {
        $supplier = Proveedor::findOrFail($id);
        return view('proveedores.show', compact('supplier'));
    }

    public function edit(string $id)
    {
        $supplier = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('supplier'));
    }

    public function update(Request $request, string $id)
    {
        $supplier = Proveedor::findOrFail($id);
        $validator = Validator::make($request->all(), Proveedor::rules($id), Proveedor::messages());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $supplier->updateProveedor($validator->validated());

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $supplier = Proveedor::findOrFail($id);
        Proveedor::deleteProveedor($supplier);

        return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado.');
    }
}
