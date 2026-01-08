<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $primaryKey = 'TRN_Codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'TRN_Codigo',
        'TRN_Nombre',
        'TRN_Tipo',
    ];
}
