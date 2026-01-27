<?php

namespace Database\Factories;

use App\Models\Factura;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacturaFactory extends Factory
{
    protected $model = Factura::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 20, 500);
        $iva = round($subtotal * 0.15, 2); // Assuming 15% IVA
        $total = $subtotal + $iva;

        return [
            'FAC_Codigo' => 'FAC-' . $this->faker->unique()->numerify('##########'),
            'CLI_Ced_Ruc' => Cliente::inRandomOrder()->first()?->CLI_Ced_Ruc ?? Cliente::factory(),
            'FAC_Subtotal' => $subtotal,
            'FAC_IVA' => $iva,
            'FAC_Total' => $total,
            'FAC_Estado' => $this->faker->randomElement(['PAGADA', 'PENDIENTE', 'CANCELADA']),
        ];
    }
}
