<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'PRO_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PRO_Codigo',
        'PRO_Nombre',
        'PRO_Descripcion_Corta',
        'PRO_Descripcion_Larga',
        'PRO_Imagen'
    ];
    //

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
