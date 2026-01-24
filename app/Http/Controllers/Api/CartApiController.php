<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use App\Models\DetalleCarrito;
use App\Models\Producto;

class CartApiController extends Controller
{
    // Helper to get current client
    private function getCliente()
    {
        $user = Auth::guard('client')->user();
        return $user ? $user->cliente : null;
    }

    // Helper to get or create active cart
    private function getCart($cliente)
    {
        if (!$cliente) return null;

        $cart = Carrito::where('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc)->first();

        if (!$cart) {
            // Manual ID generation required since it's not auto-increment
            $cart = Carrito::create([
                'CRC_Carrito' => Carrito::generateId(),
                'CLI_Ced_Ruc' => $cliente->CLI_Ced_Ruc
            ]);
        }

        return $cart;
    }

    public function index()
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['items' => [], 'count' => 0, 'total' => 0]);

        $cart = Carrito::where('CLI_Ced_Ruc', $cliente->CLI_Ced_Ruc)->with('detalles.producto')->first();

        if (!$cart) {
            return response()->json(['items' => [], 'count' => 0, 'total' => 0]);
        }

        $items = $cart->detalles->map(function ($detail) {
            $product = $detail->producto;
            $talla = $detail->CRD_Talla;

            // Determine max stock for this specific size item
            $maxStock = $product->PRO_Stock;
            if ($talla && is_array($product->PRO_Talla)) {
                foreach ($product->PRO_Talla as $s) {
                    if (isset($s['talla']) && $s['talla'] == $talla) {
                        $maxStock = $s['stock'];
                        break;
                    }
                }
            }

            return [
                'id' => $product->PRO_Codigo,
                'name' => $product->PRO_Nombre,
                'price' => (float) $product->PRO_Precio,
                'qty' => $detail->CRD_Cantidad,
                'talla' => $talla,
                'image' => asset('storage/' . $product->PRO_Imagen),
                'qt' => $maxStock
            ];
        });

        return response()->json([
            'items' => $items,
            'count' => $items->sum('qty'),
            'total' => $cart->getTotal()
        ]);
    }

    public function sync(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthenticated'], 401);

        $cart = $this->getCart($cliente);
        $localItems = $request->input('items', []);

        foreach ($localItems as $item) {
            $product = Producto::find($item['id']);
            if ($product) {
                $talla = $item['talla'] ?? null;

                // Calculate max stock
                $maxStock = $product->PRO_Stock;
                if ($talla && is_array($product->PRO_Talla)) {
                    foreach ($product->PRO_Talla as $s) {
                        if (isset($s['talla']) && $s['talla'] == $talla) {
                            $maxStock = $s['stock'];
                            break;
                        }
                    }
                }

                $detail = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
                    ->where('PRO_Codigo', $product->PRO_Codigo)
                    ->where('CRD_Talla', $talla)
                    ->first();

                if ($detail) {
                    $newQty = $detail->CRD_Cantidad + $item['qty'];
                    $detail->CRD_Cantidad = min($newQty, $maxStock);
                    $detail->save();
                } else {
                    DetalleCarrito::create([
                        'CRC_Carrito' => $cart->CRC_Carrito,
                        'PRO_Codigo' => $product->PRO_Codigo,
                        'CRD_Cantidad' => min($item['qty'], $maxStock),
                        'CRD_Talla' => $talla,
                    ]);
                }
            }
        }

        return $this->index();
    }

    public function add(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthenticated'], 401);

        $request->validate([
            'id' => 'required', // PRO_Codigo
            'qty' => 'required|integer|min:1',
            'talla' => 'nullable|string'
        ]);

        $cart = $this->getCart($cliente);
        $product = Producto::find($request->id);

        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        $talla = $request->talla;

        // Size Stock Validation
        $maxStock = $product->PRO_Stock;
        if ($talla && is_array($product->PRO_Talla)) {
            foreach ($product->PRO_Talla as $s) {
                if (isset($s['talla']) && $s['talla'] == $talla) {
                    $maxStock = $s['stock'];
                    break;
                }
            }
        }

        $detail = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
            ->where('PRO_Codigo', $product->PRO_Codigo)
            ->where('CRD_Talla', $talla)
            ->first();

        if ($detail) {
            $newQty = $detail->CRD_Cantidad + $request->qty;
            if ($newQty > $maxStock) {
                return response()->json(['message' => 'Stock insuficiente para la talla seleccionada'], 422);
            }
            $detail->CRD_Cantidad = $newQty;
            $detail->save();
        } else {
            if ($request->qty > $maxStock) {
                return response()->json(['message' => 'Stock insuficiente para la talla seleccionada'], 422);
            }
            DetalleCarrito::create([
                'CRC_Carrito' => $cart->CRC_Carrito,
                'PRO_Codigo' => $product->PRO_Codigo,
                'CRD_Cantidad' => $request->qty,
                'CRD_Talla' => $talla
            ]);
        }

        return $this->index();
    }

    public function update(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthenticated'], 401);

        $request->validate([
            'id' => 'required',
            'qty' => 'required|integer|min:0',
            'talla' => 'nullable|string'
        ]);

        $cart = $this->getCart($cliente);

        if ($request->qty == 0) {
            return $this->remove($request);
        }

        $talla = $request->talla;

        $detail = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
            ->where('PRO_Codigo', $request->id)
            ->where('CRD_Talla', $talla)
            ->first();

        if ($detail) {
            $product = Producto::find($request->id);

            // Size Stock Validation
            $maxStock = $product->PRO_Stock;
            if ($talla && is_array($product->PRO_Talla)) {
                foreach ($product->PRO_Talla as $s) {
                    if (isset($s['talla']) && $s['talla'] == $talla) {
                        $maxStock = $s['stock'];
                        break;
                    }
                }
            }

            if ($request->qty > $maxStock) {
                return response()->json(['message' => 'Stock insuficiente para la talla seleccionada'], 422);
            }
            $detail->CRD_Cantidad = $request->qty;
            $detail->save();
        }

        return $this->index();
    }

    public function remove(Request $request)
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthenticated'], 401);

        $cart = $this->getCart($cliente);

        $query = DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)
            ->where('PRO_Codigo', $request->id);

        // If talla is provided, remove precise item. If not (legacy/fallback), remove all instances of product?
        // Better to be precise.
        if ($request->has('talla')) {
            $query->where('CRD_Talla', $request->talla);
        }

        $query->delete();

        return $this->index();
    }

    public function clear()
    {
        $cliente = $this->getCliente();
        if (!$cliente) return response()->json(['message' => 'Unauthenticated'], 401);

        $cart = $this->getCart($cliente);
        DetalleCarrito::where('CRC_Carrito', $cart->CRC_Carrito)->delete();

        return $this->index();
    }
}
