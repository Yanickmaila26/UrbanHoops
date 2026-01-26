<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subcategoria extends Model
{
    use HasFactory;

    protected $table = 'subcategorias';
    protected $primaryKey = 'SCT_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'SCT_Codigo',
        'SCT_Nombre',
        'CAT_Codigo',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'CAT_Codigo', 'CAT_Codigo');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'SCT_Codigo', 'SCT_Codigo');
    }

    public static function generateId()
    {
        $last = self::orderBy('SCT_Codigo', 'desc')->first();
        if (!$last) {
            return 'SCT001';
        }
        $number = intval(substr($last->SCT_Codigo, 3)) + 1;
        return 'SCT' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // Auto-generate ID on create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->SCT_Codigo)) {
                $model->SCT_Codigo = self::generateId();
            }
        });
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
