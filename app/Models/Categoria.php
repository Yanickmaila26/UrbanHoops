<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'CAT_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CAT_Codigo',
        'CAT_Nombre'
    ];

    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class, 'CAT_Codigo', 'CAT_Codigo');
    }
}
