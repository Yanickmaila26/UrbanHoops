<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DatosFacturacion;
use App\Models\Pedido;
use App\Models\Carrito;

class CheckoutController extends Controller
{
    public function index()
    {
        // Ensure user is logged in (handled by middleware usually, but check here too)
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login');
        }

        $user = Auth::guard('client')->user();
        $cliente = $user->cliente; // Access the Cliente model related to User

        // Fetch saved billing data
        $savedProfiles = DatosFacturacion::where('DAF_CLI_Codigo', $cliente->CLI_Ced_Ruc)->get();

        return view('client.checkout.index', compact('cliente', 'savedProfiles'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'cp' => 'required|string|max:10',
            'card_number' => 'required|string|min:15|max:19', // Basic length check
            'card_expiry' => 'required|string|size:5', // MM/YY
            'card_cvv' => 'required|string|min:3|max:4',
            'cart_items' => 'required|json', // Cart items passed as JSON string
        ]);

        $user = Auth::guard('client')->user();
        $cliente = $user->cliente;

        try {
            DB::beginTransaction();

            // 1. Create/Update DatosFacturacion
            $datosFacturacion = DatosFacturacion::create([
                'DAF_CLI_Codigo' => $cliente->CLI_Ced_Ruc,
                'DAF_Direccion' => $request->direccion,
                'DAF_Ciudad' => $request->ciudad,
                'DAF_Estado' => $request->estado,
                'DAF_CP' => $request->cp,
                'DAF_Tarjeta_Numero' => $request->card_number, // Cast encrypted
                'DAF_Tarjeta_Expiracion' => $request->card_expiry,
                'DAF_Tarjeta_CVV' => $request->card_cvv, // Cast encrypted
            ]);

            // 2. Create Factura
            // Calculate totals again for security
            $cartItems = json_decode($request->cart_items, true);
            $subtotal = 0;
            foreach ($cartItems as $item) {
                // Determine quantity key (qty or quantity)
                $qty = $item['qty'] ?? $item['quantity'] ?? 1;
                $subtotal += $item['price'] * $qty;
            }
            $ivaRate = config('urbanhoops.iva', 15) / 100;
            $iva = $subtotal * $ivaRate;
            $total = $subtotal + $iva;

            $facturaId = \App\Models\Factura::generateId();

            $factura = \App\Models\Factura::create([
                'FAC_Codigo' => $facturaId,
                'CLI_Ced_Ruc' => $cliente->CLI_Ced_Ruc,
                'FAC_Subtotal' => $subtotal,
                'FAC_IVA' => $iva,
                'FAC_Total' => $total,
                'FAC_Estado' => 'Pag', // Automatically paid since it's a card checkout
            ]);

            // 3. Create DetalleFactura items
            // 3. Create DetalleFactura items
            foreach ($cartItems as $item) {
                $qty = $item['qty'] ?? $item['quantity'] ?? 1;
                $talla = $item['talla'] ?? $item['size'] ?? null;

                DB::table('detalle_factura')->insert([
                    'FAC_Codigo' => $factura->FAC_Codigo,
                    'PRO_Codigo' => $item['id'], // Assuming id matches PRO_Codigo
                    'DFC_Cantidad' => $qty,
                    'DFC_Precio' => $item['price'],
                    'DFC_Talla' => $talla,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Clear Server-Side Cart
            // DB::table('carritos')->where('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc)->delete(); OR via Model
            Carrito::where('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc)->delete();

            // 4. Create Pedido (Linked to Factura)
            $pedido = Pedido::create([
                'PED_CLI_Codigo' => $cliente->CLI_Ced_Ruc,
                'PED_DAF_Codigo' => $datosFacturacion->DAF_Codigo,
                'PED_FAC_Codigo' => $factura->FAC_Codigo,
                'PED_Fecha' => now(),
                'PED_Estado' => 'Procesando',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('client.checkout.success', ['order' => $pedido->PED_Codigo])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al procesar el pedido: ' . $e->getMessage()], 500);
        }
    }

    public function success($orderId)
    {
        $pedido = Pedido::with('factura.productos', 'datosFacturacion')->findOrFail($orderId);

        // Security check: ensure order belongs to current user
        $user = Auth::guard('client')->user();
        if ($pedido->PED_CLI_Codigo !== $user->cliente->CLI_Ced_Ruc) {
            abort(403);
        }

        return view('client.checkout.success', compact('pedido'));
    }
}
