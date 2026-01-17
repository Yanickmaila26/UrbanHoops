<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Cliente::query();

        if ($search) {
            $query->search($search);
        }

        $clientes = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'search'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            Cliente::rules(),
            Cliente::messages()
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Cliente::createCliente($validator->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    public function edit(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            Cliente::rules($id),
            Cliente::messages()
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cliente->updateCliente($validator->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        Cliente::deleteCliente($cliente);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
