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
        'PRO_Descripcion',
        'PRO_Color',
        'PRO_Talla',
        'PRO_Marca',
        'PRO_Precio',
        'PRO_Stock',
        'PRO_Imagen'
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
        return $producto->delete();
    }

    public static function rules($codigo = null)
    {
        return [
            'PRO_Codigo' => $codigo
                ? 'required|string|max:15'
                : 'required|string|max:15|unique:productos,PRO_Codigo',
            'PRO_Nombre' => [
                'required',
                'string',
                'max:60',
                'regex:/^[a-zA-Z0-9\s]+$/'
            ],
            'PRO_Descripcion' => 'required|string|max:255',
            'PRO_Color'       => 'required|string|max:15',
            'PRO_Talla'       => 'required|string|max:5',
            'PRO_Marca'       => 'required|string|max:20',
            'PRO_Precio'      => 'required|numeric|min:0',
            'PRO_Stock'       => 'required|integer|min:0',
            'PRO_Imagen'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public static function messages()
    {
        return [
            'PRO_Codigo.required' => 'El código es obligatorio.',
            'PRO_Codigo.unique'   => 'Este código ya ha sido registrado.',
            'PRO_Nombre.required' => 'El nombre del producto es obligatorio.',
            'PRO_Nombre.regex'    => 'El nombre no puede contener caracteres especiales.',
            'PRO_Precio.numeric'  => 'El precio debe ser un número válido.',
            'PRO_Precio.min'      => 'El precio no puede ser negativo.',
            'PRO_Stock.integer'   => 'El stock debe ser un número entero.',
            'PRO_Stock.min'       => 'El stock no puede ser negativo.',
            'PRO_Imagen.image'    => 'El archivo debe ser una imagen.',
            'PRO_Imagen.mimes'    => 'Formatos permitidos: jpg, jpeg, png.',
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
