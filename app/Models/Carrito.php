<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'carritos';
    protected $primaryKey = 'CRC_Carrito';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CRC_Carrito',
        'CLI_Ced_Ruc'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_Ced_Ruc', 'CLI_Ced_Ruc');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_carrito', 'CRC_Carrito', 'PRO_Codigo')
            ->using(DetalleCarrito::class)
            ->withPivot('CRD_Cantidad', 'CRD_Talla')
            ->withTimestamps();
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCarrito::class, 'CRC_Carrito', 'CRC_Carrito');
    }

    public function getSubtotal()
    {
        // Use detalles relation to leverage Model's getAttribute casing handling
        // (Pivot access on belongsToMany can likely fail with Oracle uppercase columns)
        return $this->detalles->sum(function ($detalle) {
            $precio = $detalle->producto->PRO_Precio ?? 0;
            $cantidad = $detalle->CRD_Cantidad ?? 0;
            return $precio * $cantidad;
        });
    }

    public function getIva()
    {
        // Rate is percentage (e.g., 15), convert to decimal (0.15)
        $rate = config('urbanhoops.iva', 15) / 100;
        return $this->getSubtotal() * $rate;
    }

    public function getTotal()
    {
        return $this->getSubtotal() + $this->getIva();
    }

    public static function generateId()
    {
        $last = self::orderBy('CRC_Carrito', 'desc')->first();
        if (!$last) {
            return 'CRC001';
        }
        $number = intval(substr($last->CRC_Carrito, 3)) + 1;
        return 'CRC' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
    public static function rules($id = null)
    {
        $uniqueRule = 'unique:carritos,CRC_Carrito';
        $clientUniqueRule = 'unique:carritos,CLI_Ced_Ruc';

        if ($id) {
            $uniqueRule .= ',' . $id . ',CRC_Carrito';
            $clientUniqueRule .= ',' . $id . ',CRC_Carrito';
        }

        return [
            'CRC_Carrito' => ['required', 'string', 'max:13', $uniqueRule],
            'CLI_Ced_Ruc' => ['required', 'string', 'exists:clientes,CLI_Ced_Ruc', $clientUniqueRule],
            'productos' => ['required', 'array', 'min:1'],
            'cantidades' => ['required', 'array', 'min:1'],
        ];
    }

    public static function messages()
    {
        return [
            'CRC_Carrito.required' => 'El código del carrito es obligatorio.',
            'CRC_Carrito.unique' => 'El código del carrito ya existe.',
            'CLI_Ced_Ruc.required' => 'El cliente es obligatorio.',
            'CLI_Ced_Ruc.exists' => 'El cliente seleccionado no existe.',
            'CLI_Ced_Ruc.unique' => 'Este cliente ya tiene un carrito activo.',
            'productos.required' => 'Debe seleccionar al menos un producto.',
            'productos.min' => 'Debe seleccionar al menos un producto.',
            'cantidades.required' => 'Las cantidades son obligatorias.',
        ];
    }

    /**
     * Override getAttribute to handle Oracle case-insensitive column names
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($value === null && $key !== strtolower($key)) {
            $lowerKey = strtolower($key);
            if (array_key_exists($lowerKey, $this->attributes)) {
                return $this->attributes[$lowerKey];
            }
        }

        return $value;
    }
}
