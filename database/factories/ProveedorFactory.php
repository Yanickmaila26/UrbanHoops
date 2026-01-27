<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition(): array
    {
        return [
            // 10 to 13 digits, typically 13 for RUC
            'PRV_Ced_Ruc' => $this->faker->unique()->numerify('#############'),
            'PRV_Nombre' => $this->faker->company(),
            'PRV_Direccion' => $this->faker->address(),
            'PRV_Telefono' => $this->faker->numerify('09########'),
            'PRV_Correo' => $this->faker->unique()->companyEmail(),
            'activo' => true,
        ];
    }
}
