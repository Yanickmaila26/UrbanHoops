<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $primaryKey = 'PED_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PED_Codigo',
        'PED_CLI_Codigo',
        'PED_DAF_Codigo', // Billing Data FK
        'PED_FAC_Codigo', // Invoice FK
        'PED_Fecha',
        'PED_Estado',
    ];

    /**
     * Boot function to auto-generate PED_Codigo sequentially
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->PED_Codigo)) {
                // Use SQL to find the true numeric max, ensuring concurrency safety and correct ordering
                // Oracle compatible syntax
                $maxId = \Illuminate\Support\Facades\DB::table('pedidos')
                    ->selectRaw('MAX(TO_NUMBER(SUBSTR(PED_CODIGO, 4))) as max_id')
                    ->value('max_id');

                $number = ($maxId ?? 0) + 1;
                $model->PED_Codigo = 'PED' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $casts = [
        'PED_Fecha' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'PED_CLI_CODIGO', 'CLI_CED_RUC');
    }

    public function datosFacturacion()
    {
        return $this->belongsTo(DatosFacturacion::class, 'PED_DAF_CODIGO', 'DAF_CODIGO');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'PED_FAC_CODIGO', 'FAC_CODIGO');
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
