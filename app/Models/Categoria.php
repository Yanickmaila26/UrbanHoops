<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'CAT_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CAT_Codigo',
        'CAT_Nombre',
    ];

    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class, 'CAT_Codigo', 'CAT_Codigo');
    }

    public static function generateId()
    {
        $last = self::orderBy('CAT_Codigo', 'desc')->first();
        if (!$last) {
            return 'CAT001';
        }
        $number = intval(substr($last->CAT_Codigo, 3)) + 1;
        return 'CAT' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // Auto-generate ID on create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->CAT_Codigo)) {
                $model->CAT_Codigo = self::generateId();
            }
        });
    }
}
