<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    use HasFactory;

    protected $table = 'subcategorias';
    protected $primaryKey = 'SCT_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'SCT_Codigo',
        'CAT_Codigo',
        'SCT_Nombre'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'CAT_Codigo', 'CAT_Codigo');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'SCT_Codigo', 'SCT_Codigo');
    }
}
