<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            // 10 to 13 digits for RUC/Cedula
            'CLI_Ced_Ruc' => $this->faker->unique()->numerify('##########'),
            'CLI_Nombre' => $this->faker->name(),
            'CLI_Telefono' => $this->faker->numerify('09########'),
            'CLI_Correo' => $this->faker->unique()->safeEmail(),
            'usuario_aplicacion_id' => null, // Can be assigned via state or manually if needed
        ];
    }
}
