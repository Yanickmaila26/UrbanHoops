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
        $movimientos = Bodega::getMovimientos($search);
        return view('bodegas.index', compact('movimientos', 'search'));
    }

    public function create()
    {
        $transacciones = Transaccion::all();
        $productos = Producto::all();

        // Filtrar solo las órdenes que NO han sido procesadas aún
        $ordenes = OrdenCompra::where('ORC_Estado', true)
            ->with('proveedor')
            ->get();

        $ultimo = Bodega::orderBy('created_at', 'desc')->first();
        $nuevoCodigo = $ultimo ? 'BOD-' . str_pad(Bodega::count() + 1, 4, '0', STR_PAD_LEFT) : 'BOD-0001';

        return view('bodegas.create', compact('transacciones', 'productos', 'ordenes', 'nuevoCodigo'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Bodega::rules(), Bodega::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Bodega::crearMovimiento($validator->validated());
            return redirect()->route('warehouse.index')->with('success', 'Movimiento registrado y stock actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Bodega $bodega)
    {
        return view('bodegas.show', compact('bodega'));
    }
}
