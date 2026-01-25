<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pedido;
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
     * Show the client's orders (Pedidos).
     */
    public function orders()
    {
        $client = Auth::guard('client')->user()->cliente;

        $pedidos = Pedido::with('factura')
            ->where('PED_CLI_Codigo', $client->CLI_Ced_Ruc)
            ->orderBy('PED_Fecha', 'desc')
            ->paginate(10);

        return view('client.orders', compact('pedidos'));
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
    /**
     * Show the client's billing profiles (Datos de Facturación).
     */
    public function addresses()
    {
        $client = Auth::guard('client')->user()->cliente;
        $profiles = \App\Models\DatosFacturacion::where('DAF_CLI_Codigo', $client->CLI_Ced_Ruc)->get();
        return view('client.addresses', compact('client', 'profiles'));
    }

    /**
     * Store a new billing profile.
     */
    public function storeBillingProfile(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:150',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'cp' => 'required|string|max:10',
            'telefono' => 'required|string|max:15',
            // Sensitive data like card info should be handled carefully. 
            // For now, assuming we store it for reuse as per request, but usually we'd tokenize.
            // Simplified for this scope: storing masked or provided data.
            'card_number' => 'required|string|min:15|max:19',
            'card_expiry' => 'required|string|size:5',
            'card_cvv' => 'required|string|min:3|max:4',
        ]);

        $client = Auth::guard('client')->user()->cliente;

        \App\Models\DatosFacturacion::create([
            'DAF_CLI_Codigo' => $client->CLI_Ced_Ruc,
            'DAF_Direccion' => $request->direccion,
            'DAF_Ciudad' => $request->ciudad,
            'DAF_Estado' => $request->estado,
            'DAF_CP' => $request->cp,
            'DAF_Tarjeta_Numero' => $request->card_number,
            'DAF_Tarjeta_Expiracion' => $request->card_expiry,
            'DAF_Tarjeta_CVV' => $request->card_cvv,
        ]);

        // Also update client phone if changed/provided
        $client->update(['CLI_Telefono' => $request->telefono]);

        return back()->with('success', 'Perfil de facturación agregado correctamente.');
    }

    /**
     * Delete a billing profile.
     */
    public function destroyBillingProfile($id)
    {
        $client = Auth::guard('client')->user()->cliente;
        $profile = \App\Models\DatosFacturacion::where('DAF_Codigo', $id)
            ->where('DAF_CLI_Codigo', $client->CLI_Ced_Ruc)
            ->firstOrFail();

        $profile->delete();

        return back()->with('success', 'Perfil eliminado correctamente.');
    }
}
