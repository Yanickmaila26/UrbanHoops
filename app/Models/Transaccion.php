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
