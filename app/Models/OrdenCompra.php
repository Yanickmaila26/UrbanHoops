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

    public static function rules($numeroActual = null)
    {
        return [
            'ORC_Numero' => 'required|string|unique:orden_compras,ORC_Numero,' . $numeroActual . ',ORC_Numero',
            'PRV_Ced_Ruc' => 'required|exists:proveedors,PRV_Ced_Ruc',
            'ORC_Fecha_Emision' => 'required|date',
            'ORC_Fecha_Entrega' => 'required|date|after_or_equal:ORC_Fecha_Emision',
            'ORC_Monto_Total' => 'required|numeric|min:0',
            'productos' => 'required|array|min:1',
            'cantidades' => 'required|array|min:1',
        ];
    }
    public static function messages()
    {
        return [
            'ORC_Numero.required' => 'El número de orden es obligatorio.',
            'ORC_Numero.unique'   => 'Este número de orden ya ha sido registrado.',

            'PRV_Ced_Ruc.required' => 'Debe seleccionar un proveedor.',
            'PRV_Ced_Ruc.exists'   => 'El proveedor seleccionado no es válido.',

            'ORC_Fecha_Emision.required' => 'La fecha de emisión es obligatoria.',
            'ORC_Fecha_Emision.date'     => 'La fecha de emisión debe ser una fecha válida.',
            'ORC_Fecha_Entrega.required' => 'La fecha de entrega es obligatoria.',
            'ORC_Fecha_Entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a la fecha de emisión.',

            'ORC_Monto_Total.required' => 'El monto total es obligatorio.',
            'ORC_Monto_Total.numeric'  => 'El monto debe ser un valor numérico.',
            'ORC_Monto_Total.min'      => 'El monto total no puede ser negativo.',

            'productos.required'   => 'Debe agregar al menos un producto a la orden.',
            'productos.min'        => 'Debe agregar al menos un producto a la orden.',
            'cantidades.required'  => 'Debe especificar las cantidades para cada producto.',
            'cantidades.*.integer' => 'La cantidad debe ser un número entero.',
            'cantidades.*.min'     => 'La cantidad mínima por producto es 1.',
        ];
    }
}
