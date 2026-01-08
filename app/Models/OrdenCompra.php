<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    protected $table = 'orden_compras';
    protected $primaryKey = 'ORC_Numero';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ORC_Numero',
        'PRV_Ced_Ruc',
        'ORC_Fecha_Emision',
        'ORC_Fecha_Entrega',
        'ORC_Monto_Total',
        'ORC_Estado'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'PRV_Ced_Ruc', 'PRV_Ced_Ruc');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_ord_com', 'ORC_Numero', 'PRO_Codigo')
            ->withPivot('cantidad_solicitada');
    }

    public static function getOrdenes($search = null)
    {
        $query = self::with(['proveedor']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ORC_Numero', 'like', "%{$search}%")
                    ->orWhere('PRV_Ced_Ruc', 'like', "%{$search}%")
                    ->orWhereHas('proveedor', function ($pro) use ($search) {
                        $pro->where('PRV_Nombre', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
    }

    public static function createOrden(array $data, array $productos)
    {
        $orden = self::create($data);
        $orden->productos()->attach($productos);
        return $orden;
    }

    public function updateOrden(array $data, array $productos)
    {
        // Solo permite si está activa
        if (!$this->ORC_Estado) {
            throw new \Exception("No se puede editar una orden finalizada o inactiva.");
        }
        $this->update($data);
        // Sync actualiza la tabla intermedia (borra los viejos y pone los nuevos)
        $this->productos()->sync($productos);
        return $this;
    }

    public static function deleteOrden(OrdenCompra $orden)
    {
        if (!$orden->ORC_Estado) {
            throw new \Exception("No se puede eliminar una orden que no esté activa.");
        }
        return $orden->delete();
    }
}
