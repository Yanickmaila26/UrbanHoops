<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carrito;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarritoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Carrito::with(['cliente', 'productos']);

        if ($search) {
            $query->where('CRC_Carrito', 'like', "%{$search}%")
                ->orWhere('CLI_Ced_Ruc', 'like', "%{$search}%")
                ->orWhereHas('cliente', function ($q) use ($search) {
                    $q->where('CLI_Nombre', 'like', "%{$search}%") // Asumiendo campo nombre en cliente
                        ->orWhere('CLI_Apellido', 'like', "%{$search}%");
                });
        }

        $carritos = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('carritos.index', compact('carritos', 'search'));
    }

    public function create()
    {
        $ultimo = Carrito::orderBy('CRC_Carrito', 'desc')->first();
        $nuevoCodigo = $ultimo
            ? 'CRC' . str_pad((int)substr($ultimo->CRC_Carrito, 3) + 1, 3, '0', STR_PAD_LEFT)
            : 'CRC001';

        $clientes = Cliente::whereNotIn('CLI_Ced_Ruc', Carrito::pluck('CLI_Ced_Ruc'))->get();
        $productos = Producto::all();

        return view('carritos.create', compact('nuevoCodigo', 'clientes', 'productos'));
    }

    public function store(Request $request)
    {
        // Validar
        // Validar
        $request->validate(Carrito::rules(), Carrito::messages());

        if (count($request->productos) !== count(array_unique($request->productos))) {
            return redirect()->back()->with('error', 'No se permiten productos duplicados.')->withInput();
        }

        try {
            DB::beginTransaction();

            $carrito = Carrito::create([
                'CRC_Carrito' => $request->CRC_Carrito,
                'CLI_Ced_Ruc' => $request->CLI_Ced_Ruc
            ]);

            $detalles = [];
            foreach ($request->productos as $index => $proCodigo) {
                $detalles[$proCodigo] = ['CRD_Cantidad' => $request->cantidades[$index]];
            }
            $carrito->productos()->sync($detalles);

            DB::commit();
            return redirect()->route('shopping-carts.index')->with('success', 'Carrito creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al crear el carrito: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $carrito = Carrito::with(['cliente', 'productos'])->findOrFail($id);
        // Podríamos retornar view('carritos.show') si existiera, o reusar la logica de index/edit
        return view('carritos.show', compact('carrito'));
    }

    public function edit($id)
    {
        $carrito = Carrito::with('productos')->findOrFail($id);
        $clientes = Cliente::all();
        $productos = Producto::all();

        return view('carritos.edit', compact('carrito', 'clientes', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $carrito = Carrito::findOrFail($id);

        // Validar
        $request->validate(
            \Illuminate\Support\Arr::except(Carrito::rules($id), ['CRC_Carrito']),
            Carrito::messages()
        );

        // Validar duplicados
        if (count($request->productos) !== count(array_unique($request->productos))) {
            return redirect()->back()->with('error', 'No se permiten productos duplicados.')->withInput();
        }

        try {
            DB::beginTransaction();

            $carrito->update([
                'CLI_Ced_Ruc' => $request->CLI_Ced_Ruc
            ]);

            $detalles = [];
            foreach ($request->productos as $index => $proCodigo) {
                $detalles[$proCodigo] = ['CRD_Cantidad' => $request->cantidades[$index]];
            }
            $carrito->productos()->sync($detalles);

            DB::commit();
            return redirect()->route('shopping-carts.index')->with('success', 'Carrito actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $carrito = Carrito::findOrFail($id);
        try {
            $carrito->delete();
            return redirect()->route('shopping-carts.index')->with('success', 'Carrito eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('shopping-carts.index')->with('error', 'No se pudo eliminar el carrito.');
        }
    }
}
