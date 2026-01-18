<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kardex;
use App\Models\Transaccion;
use App\Models\Producto;
use App\Models\OrdenCompra;
use App\Models\Bodega;
use Illuminate\Support\Facades\Validator;

class KardexController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $movimientos = Kardex::getMovimientos($search);
        return view('kardex.index', compact('movimientos', 'search'));
    }

    public function create()
    {
        $transacciones = Transaccion::all();
        $productos = Producto::all();
        $bodegas = Bodega::all(); // Load available warehouses

        // Filtrar solo las órdenes que NO han sido procesadas aún (y activas)
        $ordenes = OrdenCompra::where('ORC_Estado', true)
            ->with('proveedor')
            ->get();

        $nuevoCodigo = Kardex::generateId();

        return view('kardex.create', compact('transacciones', 'productos', 'ordenes', 'nuevoCodigo', 'bodegas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Kardex::rules(), Kardex::messages());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Kardex::crearMovimiento($validator->validated());
            return redirect()->route('kardex.index')->with('success', 'Movimiento registrado y stock actualizado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
