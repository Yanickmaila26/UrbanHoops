<?php

namespace Database\Factories;

use App\Models\DatosFacturacion;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class DatosFacturacionFactory extends Factory
{
    protected $model = DatosFacturacion::class;

    public function definition(): array
    {
        return [
            'DAF_Codigo' => 'DAF-' . $this->faker->unique()->bothify('?????#####'),
            'DAF_CLI_Codigo' => Cliente::inRandomOrder()->first()?->CLI_Ced_Ruc ?? Cliente::factory(),
            'DAF_Direccion' => $this->faker->address(),
            'DAF_Ciudad' => $this->faker->city(),
            'DAF_Estado' => $this->faker->state(),
            'DAF_CP' => $this->faker->postcode(),
            'DAF_Tarjeta_Numero' => $this->faker->creditCardNumber(),
            'DAF_Tarjeta_Expiracion' => $this->faker->creditCardExpirationDateString(),
            'DAF_Tarjeta_CVV' => $this->faker->numerify('###'),
        ];
    }
}
