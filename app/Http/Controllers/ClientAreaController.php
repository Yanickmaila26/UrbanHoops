<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Carrito;

class ClientAreaController extends Controller
{
    /**
     * Show the main client dashboard (Mi Cuenta).
     */
    public function index()
    {
        // For now, redirect to orders or show a welcome page.
        // Let's show the orders page as the main dashboard for utility.
        return redirect()->route('client.orders');
    }

    /**
     * Show the client's cart (Shopping Cart).
     * Reuses the public/admin cart logic but scoped to the client.
     */
    public function cart()
    {
        $client = Auth::guard('client')->user()->cliente;
        if (!$client) {
            abort(403, 'Perfil de cliente no encontrado.');
        }

        // Find active cart for this client
        // logic reused from CarritoController but simplified for view
        $carrito = Carrito::where('CLI_Ced_Ruc', $client->CLI_Ced_Ruc)->first();

        return view('client.cart', compact('carrito'));
    }

    /**
     * Show the client's orders (Facturas).
     */
    public function orders()
    {
        $client = Auth::guard('client')->user()->cliente;

        $facturas = Factura::where('CLI_Ced_Ruc', $client->CLI_Ced_Ruc)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.orders', compact('facturas'));
    }

    /**
     * Show the client's invoices (Facturas).
     * Same as orders in this context, but maybe different view if needed.
     * For now, alias to orders or show specifically paid ones.
     */
    public function invoices()
    {
        // Could filter by state if needed, or just show same list
        return $this->orders();
    }

    /**
     * Show the client's addresses (Profile).
     */
    public function addresses()
    {
        $client = Auth::guard('client')->user()->cliente;
        return view('client.addresses', compact('client'));
    }

    /**
     * Update client address.
     */
    public function updateAddress(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:150',
            'telefono' => 'required|string|size:10|regex:/^[0-9]+$/',
        ]);

        $client = Auth::guard('client')->user()->cliente;
        $client->update([
            'CLI_Direccion' => $request->direccion,
            'CLI_Telefono' => $request->telefono,
        ]);

        return back()->with('success', 'Información actualizada correctamente.');
    }
}
