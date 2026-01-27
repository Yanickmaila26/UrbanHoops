<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
    protected $table = 'facturas';
    protected $primaryKey = 'FAC_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'FAC_Codigo',
        'CLI_Ced_Ruc',
        'FAC_Subtotal',
        'FAC_IVA',
        'FAC_Total',
        'FAC_Estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_Ced_Ruc', 'CLI_Ced_Ruc');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_factura', 'FAC_CODIGO', 'PRO_CODIGO')
            ->withPivot('DFC_CANTIDAD', 'DFC_PRECIO', 'DFC_TALLA')
            ->withTimestamps();
    }

    public static function generateId()
    {
        // Use SQL to find the true numeric max, ensuring concurrency safety and correct ordering
        // Oracle compatible syntax
        $maxId = \Illuminate\Support\Facades\DB::table('facturas')
            ->selectRaw('MAX(TO_NUMBER(SUBSTR(FAC_CODIGO, 4))) as max_id')
            ->value('max_id');

        $number = ($maxId ?? 0) + 1;
        return 'FAC' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function rules($id = null)
    {
        $uniqueRule = 'unique:facturas,FAC_Codigo';
        if ($id) {
            $uniqueRule .= ',' . $id . ',FAC_Codigo';
        }

        return [
            'FAC_Codigo' => ['required', 'string', 'max:15', $uniqueRule],
            'CLI_Ced_Ruc' => ['required', 'string', 'exists:clientes,CLI_Ced_Ruc'],
            'productos' => ['required', 'array', 'min:1'],
            'cantidades' => ['required', 'array', 'min:1'],
            'precios' => ['required', 'array', 'min:1'], // Invoice stores snapshot price
        ];
    }

    public static function messages()
    {
        return [
            'FAC_Codigo.required' => 'El código de factura es obligatorio.',
            'FAC_Codigo.unique' => 'El código de factura ya existe.',
            'CLI_Ced_Ruc.required' => 'El cliente es obligatorio.',
            'CLI_Ced_Ruc.exists' => 'El cliente seleccionado no existe.',
            'productos.required' => 'Debe agregar al menos un producto.',
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
