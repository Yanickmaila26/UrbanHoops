<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proveedors';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'PRV_Ced_Ruc';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'PRV_Ced_Ruc',
        'nombre',
        'PRV_Direccion',
        'PRV_Telefono',
        'PRV_Correo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Validation rules for the model.
     *
     * @return array
     */
    public static function rules($id = null)
    {
        $rules = [
            'PRV_Ced_Ruc' => 'required|string|size:13|unique:proveedores,PRV_Ced_Ruc',
            'nombre' => 'required|string|max:255',
            'PRV_Direccion' => 'required|string|max:150',
            'PRV_Telefono' => 'required|string|size:10|regex:/^[0-9]{10}$/',
            'PRV_Correo' => 'required|email|max:60',
        ];

        if ($id) {
            $rules['PRV_Ced_Ruc'] = 'required|string|size:13|unique:proveedores,PRV_Ced_Ruc,' . $id . ',PRV_Ced_Ruc';
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public static function messages()
    {
        return [
            'PRV_Ced_Ruc.required' => 'La cédula/RUC es obligatoria.',
            'PRV_Ced_Ruc.size' => 'La cédula/RUC debe tener 13 dígitos.',
            'PRV_Ced_Ruc.unique' => 'La cédula/RUC ya está registrada.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
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

    /**
     * Scope para búsqueda.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('PRV_Ced_Ruc', 'like', "%{$search}%")
                ->orWhere('nombre', 'like', "%{$search}%")
                ->orWhere('PRV_Correo', 'like', "%{$search}%")
                ->orWhere('PRV_Telefono', 'like', "%{$search}%");
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'PRV_Ced_Ruc';
    }
}
