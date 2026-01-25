<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosFacturacion extends Model
{
    use HasFactory;

    protected $table = 'datos_facturacion';
    protected $primaryKey = 'DAF_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'DAF_Codigo',
        'DAF_CLI_Codigo',
        'DAF_Direccion',
        'DAF_Ciudad',
        'DAF_Estado',
        'DAF_CP',
        'DAF_Tarjeta_Numero',
        'DAF_Tarjeta_Expiracion',
        'DAF_Tarjeta_CVV',
    ];

    /**
     * Boot function to auto-generate DAF_Codigo
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->DAF_Codigo)) {
                $model->DAF_Codigo = 'DAF-' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'DAF_Tarjeta_Numero' => 'encrypted',
        'DAF_Tarjeta_CVV' => 'encrypted',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'DAF_CLI_Codigo', 'CLI_Ced_Ruc');
    }
}
