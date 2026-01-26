<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'CLI_Ced_Ruc';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CLI_Ced_Ruc',
        'CLI_Nombre',
        'CLI_Telefono',
        'CLI_Correo',
        'usuario_aplicacion_id',
    ];
    public static function getClientes($search = null)
    {
        $query = self::query();

        if ($search) {
            $query->search($search);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    public static function createCliente(array $data)
    {
        return self::create($data);
    }

    public function updateCliente(array $data)
    {
        return $this->update($data);
    }

    public static function deleteCliente(Cliente $cliente)
    {
        return $cliente->delete();
    }

    public static function rules($id = null)
    {
        return [
            'CLI_Ced_Ruc' => 'required|string|min:10|max:13|unique:clientes,CLI_Ced_Ruc,' . $id . ',CLI_Ced_Ruc',
            'CLI_Nombre' => 'required|string|max:60|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/',
            'CLI_Telefono' => 'required|string|size:10|regex:/^[0-9]+$/',
            'CLI_Correo' => 'required|email|max:60|unique:clientes,CLI_Correo,' . $id . ',CLI_Ced_Ruc',
        ];
    }

    public static function messages()
    {
        return [
            'CLI_Ced_Ruc.required' => 'La cédula/RUC es obligatoria.',
            'CLI_Ced_Ruc.min' => 'La cédula/RUC debe tener al menos 10 dígitos.',
            'CLI_Ced_Ruc.max' => 'La cédula/RUC debe tener máximo 13 dígitos.',
            'CLI_Ced_Ruc.unique' => 'La cédula/RUC ya está registrada.',
            'CLI_Nombre.required' => 'El nombre es obligatorio.',
            'CLI_Nombre.max' => 'El nombre no debe exceder los 60 caracteres.',
            'CLI_Nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'CLI_Telefono.required' => 'El teléfono es obligatorio.',
            'CLI_Telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
            'CLI_Telefono.regex' => 'El teléfono debe contener solo números.',
            'CLI_Correo.required' => 'El correo electrónico es obligatorio.',
            'CLI_Correo.email' => 'El correo electrónico no es válido.',
            'CLI_Correo.max' => 'El correo electrónico no debe exceder los 60 caracteres.',
            'CLI_Correo.unique' => 'El correo electrónico ya está registrado.',
        ];
    }
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('CLI_Ced_Ruc', 'like', "%{$search}%")
                ->orWhere('CLI_Nombre', 'like', "%{$search}%")
                ->orWhere('CLI_Correo', 'like', "%{$search}%")
                ->orWhere('CLI_Telefono', 'like', "%{$search}%");
        });
    }
    public function usuarioAplicacion()
    {
        return $this->belongsTo(UsuarioAplicacion::class, 'usuario_aplicacion_id');
    }

    /**
     * Override getAttribute to handle Oracle case-insensitive column names
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
