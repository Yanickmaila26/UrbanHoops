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
        'PRV_Ced_Ruc',
        'PRO_Nombre',
        'PRO_Descripcion',
        'PRO_Color',
        'PRO_Talla',
        'PRO_Marca',
        'PRO_Precio',
        'PRO_Stock',
        'PRO_Imagen',
        'activo'
    ];

    public static function getProductos($search = null)
    {
        $query = self::query();
        if ($search) {
            $query->search($search);
        }
        return $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    public static function createProducto(array $data)
    {
        return self::create($data);
    }

    public function updateProducto(array $data)
    {
        return $this->update($data);
    }

    public static function deleteProducto(Producto $producto)
    {
        return $producto->update(['activo' => false]);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'PRV_Ced_Ruc', 'PRV_Ced_Ruc');
    }

    public static function rules($id = null)
    {
        return [
            'PRO_Codigo' => 'required|string|max:15|unique:productos,PRO_Codigo,' . $id . ',PRO_Codigo',
            'PRV_Ced_Ruc' => 'required|exists:proveedors,PRV_Ced_Ruc',
            'PRO_Nombre' => 'required|string|max:100|regex:/^[A-Za-z0-9\sáéíóúÁÉÍÓÚñÑ.]+$/',
            'PRO_Descripcion' => 'required|string|max:255',
            'PRO_Color'       => 'required|string|max:15',
            'PRO_Talla'       => 'required|string|max:5',
            'PRO_Marca'       => 'required|string|max:20',
            'PRO_Precio'      => 'required|numeric|min:0',
            'PRO_Stock'       => 'required|integer|min:0',
            'PRO_Imagen'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public static function messages()
    {
        return [
            'PRO_Codigo.required'      => 'El código es obligatorio.',
            'PRO_Codigo.unique'        => 'Este código ya ha sido registrado.',
            'PRV_Ced_Ruc.required'     => 'El proveedor es obligatorio.',
            'PRV_Ced_Ruc.exists'       => 'El proveedor seleccionado no es válido.',
            'PRO_Nombre.required'      => 'El nombre del producto es obligatorio.',
            'PRO_Nombre.regex'         => 'El nombre no puede contener caracteres especiales.',
            'PRO_Descripcion.required' => 'La descripción es obligatoria.',
            'PRO_Color.required'       => 'El color es obligatorio.',
            'PRO_Talla.required'       => 'La talla es obligatoria.',
            'PRO_Marca.required'       => 'La marca es obligatoria.',
            'PRO_Precio.required'      => 'El precio es obligatorio.',
            'PRO_Precio.numeric'       => 'El precio debe ser un número válido.',
            'PRO_Precio.min'           => 'El precio no puede ser negativo.',
            'PRO_Stock.required'       => 'El stock inicial es obligatorio.',
            'PRO_Stock.integer'        => 'El stock debe ser un número entero.',
            'PRO_Stock.min'            => 'El stock no puede ser negativo.',
            'PRO_Imagen.image'         => 'El archivo debe ser una imagen.',
            'PRO_Imagen.mimes'         => 'Formatos permitidos: jpg, jpeg, png.',
            'PRO_Imagen.max'           => 'La imagen no debe pesar más de 2MB.',
        ];
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('PRO_Codigo', 'like', "%{$search}%")
                ->orWhere('PRO_Nombre', 'like', "%{$search}%")
                ->orWhere('PRO_Marca', 'like', "%{$search}%")
                ->orWhere('PRO_Color', 'like', "%{$search}%");
        });
    }
}
