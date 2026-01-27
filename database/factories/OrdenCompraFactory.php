<?php

namespace Database\Factories;

use App\Models\OrdenCompra;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdenCompraFactory extends Factory
{
    protected $model = OrdenCompra::class;

    public function definition(): array
    {
        $fechaEmision = $this->faker->dateTimeBetween('-1 year', 'now');
        $fechaEntrega = (clone $fechaEmision)->modify('+' . rand(1, 30) . ' days');

        return [
            'ORC_Numero' => 'ORD-' . $this->faker->unique()->numerify('##########'),
            'PRV_Ced_Ruc' => Proveedor::inRandomOrder()->first()?->PRV_Ced_Ruc ?? Proveedor::factory(),
            'ORC_Fecha_Emision' => $fechaEmision,
            'ORC_Fecha_Entrega' => $fechaEntrega,
            'ORC_Monto_Total' => $this->faker->randomFloat(2, 100, 10000),
            'ORC_Estado' => $this->faker->randomElement(['PENDIENTE', 'APROBADA', 'RECIBIDA']),
        ];
    }
}
