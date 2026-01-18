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
            ->withPivot('CRD_Cantidad')
            ->withTimestamps();
    }

    public function getTotal()
    {
        return $this->productos->sum(function ($producto) {
            return $producto->PRO_Precio * $producto->pivot->CRD_Cantidad;
        });
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
}
