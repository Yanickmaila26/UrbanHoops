<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    use HasFactory;
    protected $table = 'bodegas';
    protected $primaryKey = 'BOD_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'BOD_Codigo',
        'BOD_Nombre',
        'BOD_Direccion',
        'BOD_Ciudad',
        'BOD_Pais',
        'BOD_CodigoPostal',
        'BOD_Responsable'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->BOD_Codigo) {
                $last = self::orderBy('BOD_Codigo', 'desc')->first();
                $number = $last ? intval(substr($last->BOD_Codigo, 4)) + 1 : 1;
                $model->BOD_Codigo = 'BOD-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public static function rules($id = null)
    {
        return [
            'BOD_Nombre' => 'required|string|max:30',
            'BOD_Direccion' => 'required|string|max:50',
            'BOD_Ciudad' => 'required|string|max:30',
            'BOD_Pais' => 'required|string|max:30',
            'BOD_CodigoPostal' => 'required|string|max:10',
            'BOD_Responsable' => 'required|string|max:50',
        ];
    }

    public static function messages()
    {
        return [
            'BOD_Nombre.required' => 'El nombre es obligatorio.',
            'BOD_Direccion.required' => 'La dirección es obligatoria.',
            'BOD_Ciudad.required' => 'La ciudad es obligatoria.',
            'BOD_Pais.required' => 'El país es obligatorio.',
            'BOD_CodigoPostal.required' => 'El código postal es obligatorio.',
            'BOD_Responsable.required' => 'El responsable es obligatorio.',
        ];
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_bodega', 'BOD_Codigo', 'PRO_Codigo')
            ->withPivot('PXB_Stock')
            ->withTimestamps();
    }

    /**
     * Override parameter handling to support insensitive database column names
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
