<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    protected $table = 'proveedors';
    protected $primaryKey = 'PRV_Ced_Ruc';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PRV_Ced_Ruc',
        'PRV_Nombre',
        'PRV_Direccion',
        'PRV_Telefono',
        'PRV_Correo',
        'activo',
    ];

    public static function getProveedores($search = null)
    {
        $query = self::query();

        if ($search) {
            $query->search($search);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    public static function getProveedoresActivos()
    {
        return self::activos()->orderBy('PRV_Nombre', 'asc')->get();
    }

    public static function createProveedor(array $data)
    {
        return self::create($data);
    }

    public function updateProveedor(array $data)
    {
        return $this->update($data);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public static function deleteProveedor(Proveedor $proveedor)
    {
        return $proveedor->update(['activo' => false]);
    }

    public static function rules($id = null)
    {
        return [
            'PRV_Ced_Ruc' => 'required|string|min:10|max:13|unique:proveedors,PRV_Ced_Ruc,' . $id . ',PRV_Ced_Ruc',
            'PRV_Nombre'    => 'required|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/',
            'PRV_Direccion' => 'required|string|max:150',
            'PRV_Telefono'  => 'required|string|size:10|regex:/^[0-9]+$/',
            'PRV_Correo'    => 'required|email|max:60',
        ];
    }

    public static function messages()
    {
        return [
            'PRV_Ced_Ruc.required' => 'La cédula/RUC es obligatoria.',
            'PRV_Ced_Ruc.max' => 'La cédula/RUC debe tener 13 dígitos máximo.',
            'PRV_Ced_Ruc.min' => 'La cédula/RUC debe tener 10 dígitos minimo.',
            'PRV_Ced_Ruc.unique' => 'La cédula/RUC ya está registrada.',
            'PRV_Nombre.required' => 'El nombre es obligatorio.',
            'PRV_Nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'PRV_Nombre.regex' => 'Solo se permiten letras y espacios en el nombre.',
            'PRV_Direccion.required' => 'La dirección es obligatoria.',
            'PRV_Direccion.max' => 'La dirección no debe exceder los 150 caracteres.',
            'PRV_Telefono.required' => 'El teléfono es obligatorio.',
            'PRV_Telefono.size' => 'El teléfono debe tener 10 dígitos.',
            'PRV_Telefono.regex' => 'El teléfono debe contener solo números.',
            'PRV_Correo.required' => 'El correo electrónico es obligatorio.',
            'PRV_Correo.email' => 'El correo electrónico no es válido.',
            'PRV_Correo.max' => 'El correo electrónico no debe exceder los 60 caracteres.',
        ];
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('PRV_Ced_Ruc', 'like', "%{$search}%")
                ->orWhere('PRV_Nombre', 'like', "%{$search}%")
                ->orWhere('PRV_Correo', 'like', "%{$search}%")
                ->orWhere('PRV_Telefono', 'like', "%{$search}%");
        });
    }

    /**
     * Override parameter handling to support insensitive database column names
     * (Fixes OCI8/PDO lowercase return issue)
     */
    public function getAttribute($key)
    {
        // Try standard access
        $value = parent::getAttribute($key);

        // If null, try lowercase key if attributes exist
        if ($value === null && $key !== strtolower($key)) {
            $lowerKey = strtolower($key);
            if (array_key_exists($lowerKey, $this->attributes)) {
                return $this->attributes[$lowerKey];
            }
        }

        return $value;
    }
}
