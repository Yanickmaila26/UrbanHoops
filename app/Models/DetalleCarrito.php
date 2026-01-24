<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model
{
    protected $table = 'detalle_carrito';

    // Pivot table usually doesn't have a single primary key, 
    // but Eloquent Model expects one. We can disable it or use composite keys integration if needed.
    // For simple CRUD via ID, it's hard without an ID column.
    // However, we are querying by CRC_Carrito + PRO_Codigo, so we can manage without AI ID.
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'CRC_Carrito',
        'PRO_Codigo',
        'CRD_Cantidad',
        'CRD_Talla'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Codigo', 'PRO_Codigo');
    }

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'CRC_Carrito', 'CRC_Carrito');
    }

    // Accessor for price from product relationship (if needed by JSON serialization)
    public function getProductPriceAttribute()
    {
        return $this->producto ? $this->producto->PRO_Precio : 0;
    }
}
