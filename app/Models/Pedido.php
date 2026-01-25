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
     * Boot function to auto-generate PED_Codigo
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->PED_Codigo)) {
                $model->PED_Codigo = 'PED-' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            }
        });
    }

    protected $casts = [
        'PED_Fecha' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'PED_CLI_Codigo', 'CLI_Ced_Ruc');
    }

    public function datosFacturacion()
    {
        return $this->belongsTo(DatosFacturacion::class, 'PED_DAF_Codigo', 'DAF_Codigo');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'PED_FAC_Codigo', 'FAC_Codigo');
    }
}
