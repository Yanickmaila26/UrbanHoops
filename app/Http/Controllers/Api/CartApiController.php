<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\Producto;

class CartApiController extends Controller
{
    /**
     * Helper: Get current client from authenticated user OR session
     */
    private function getCliente()
    {
        $user = Auth::guard('client')->user() ?? Auth::guard('web')->user() ?? Auth::user();
        return $user && isset($user->cliente) ? $user->cliente : null;
    }

    /**
     * Helper: Get guest cart ID from session
     */
    private function getGuestCartId()
    {
        if (!session()->has('guest_cart_id')) {
            session(['guest_cart_id' => 'GUEST_' . uniqid()]);
        }
        return session('guest_cart_id');
    }

    /**
     * Helper: Get or Create active cart for client OR guest
     */
    private function getCart($cliente = null)
    {
        // Authenticated user cart
        if ($cliente) {
            $cart = Carrito::where('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc)->first();

            if (!$cart) {
                $cart = Carrito::create([
                    'CRC_Carrito' => Carrito::generateId(),
                    'CLI_Ced_Ruc' => $cliente->CLI_Ced_Ruc
                ]);
            }

            return $cart;
        }

        // Guest cart (Session-based)
        $guestCartId = $this->getGuestCartId();
        $cart = Carrito::where('CRC_Carrito', $guestCartId)->first();

        if (!$cart) {
            $cart = Carrito::create([
                'CRC_Carrito' => $guestCartId,
                'CLI_Ced_Ruc' => null // Guest cart has no client
            ]);
        }

        return $cart;
    }

    /**
     * Get cart items formatted for frontend
     */
    public function index(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) {
            return response()->json(['items' => []]);
        }

        $cart = Carrito::where('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc)
            ->with('detalles.producto')
            ->first();

        if (!$cart) {
            return response()->json(['items' => []]);
        }

        $items = $cart->detalles->map(function ($detail) {
            $product = $detail->producto;
            if (!$product) return null;

            $talla = $detail->CRD_Talla;
            $stock = 9999;

            if ($talla && is_array($product->PRO_Talla)) {
                foreach ($product->PRO_Talla as $s) {
                    if (is_array($s) && isset($s['talla']) && $s['talla'] == $talla) {
                        $stock = $s['stock'];
                        break;
                    }
                }
            } elseif (is_numeric($product->PRO_Stock)) {
                $stock = $product->PRO_Stock;
            }

            return [
                'id' => $product->PRO_Codigo,
                'name' => $product->PRO_Nombre,
                'price' => (float) $product->PRO_Precio,
                'qty' => (int) $detail->CRD_Cantidad,
                'image' => $product->PRO_Imagen ? asset('storage/' . $product->PRO_Imagen) : asset('images/default.jpg'),
                'talla' => $talla,
                'qt' => $stock
            ];
        })->filter()->values();

        return response()->json(['items' => $items]);
    }

    /**
     * Sync localStorage cart with Database
     */
    public function sync(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthorized'], 401);

        DB::beginTransaction();
        try {
            $cart = $this->getCart($cliente);

            // Check if DB has items
            $dbCount = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)->count();

            if ($dbCount > 0) {
                // DB has priority, return current DB state
                DB::commit();
                return $this->index($request);
            }

            // DB is empty, import logic
            $localItems = $request->input('items', []);

            foreach ($localItems as $item) {
                $prod = Producto::find($item['id']);
                if (!$prod) continue;

                DetalleCarrito::create([
                    'CRC_Carrito' => $cart->CRC_Carrito,
                    'PRO_Codigo' => $prod->PRO_Codigo,
                    'CRD_Cantidad' => $item['qty'],
                    'CRD_Talla' => $item['talla'] ?? null
                ]);
            }

            DB::commit();
            return $this->index($request);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function add(Request $request)
    {
        $cliente = $this->getCliente();
        $cart = $this->getCart($cliente); // Works for both authenticated and guest

        $item = $request->all();

        // Build the query to find the specific item (Product + Talla)
        $query = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
            ->where('PRO_Codigo', $item['id']);

        if (isset($item['talla']) && $item['talla']) {
            $query->where('CRD_Talla', $item['talla']);
        } else {
            $query->whereNull('CRD_Talla');
        }

        $existing = $query->first();

        if ($existing) {
            // Update using Query Builder logic to avoid composite key issues with Eloquent save()
            $newQty = $existing->CRD_Cantidad + $item['qty'];

            // Re-build query to ensure exact match update
            DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
                ->where('PRO_Codigo', $item['id'])
                ->where(function ($q) use ($item) {
                    if (isset($item['talla']) && $item['talla']) {
                        $q->where('CRD_Talla', $item['talla']);
                    } else {
                        $q->whereNull('CRD_Talla');
                    }
                })
                ->update(['CRD_Cantidad' => $newQty]);
        } else {
            DetalleCarrito::create([
                'CRC_Carrito' => $cart->CRC_Carrito,
                'PRO_Codigo' => $item['id'],
                'CRD_Cantidad' => $item['qty'],
                'CRD_Talla' => $item['talla'] ?? null
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function remove(Request $request, $itemId)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthorized'], 401);

        $talla = $request->input('talla');
        if ($talla === 'null') $talla = null;

        $cart = $this->getCart($cliente);

        $query = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
            ->where('PRO_Codigo', $itemId);

        if ($talla) {
            $query->where('CRD_Talla', $talla);
        } else {
            $query->whereNull('CRD_Talla');
        }

        $query->delete();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $itemId)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthorized'], 401);

        $qty = $request->input('qty');
        $talla = $request->input('talla');
        if ($talla === 'null') $talla = null;

        $cart = $this->getCart($cliente);

        // Update directly using Query Builder
        $query = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
            ->where('PRO_Codigo', $itemId);

        if ($talla) {
            $query->where('CRD_Talla', $talla);
        } else {
            $query->whereNull('CRD_Talla');
        }

        // Perform update on the query builder directly, avoiding model->save()
        $query->update(['CRD_Cantidad' => $qty]);

        return response()->json(['success' => true]);
    }
}
