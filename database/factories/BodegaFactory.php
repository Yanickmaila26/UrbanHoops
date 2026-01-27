<?php

namespace Database\Factories;

use App\Models\Bodega;
use Illuminate\Database\Eloquent\Factories\Factory;

class BodegaFactory extends Factory
{
    protected $model = Bodega::class;

    public function definition(): array
    {
        // BOD-#### logic is in model boot(), but for factory we might want explicit control or let model handle it.
        // If we let model handle it, we leave BOD_Codigo null. 
        // However, massive generation might conflict if we rely on sequential reads in boot().
        // Better to generate random unique here for safety in mass inserts if boot isn't used (e.g. insert())
        // But factories normally use create(), triggering boot.

        static $sequence = 1;

        return [
            'BOD_Codigo' => 'BOD-' . str_pad($sequence++, 6, '0', STR_PAD_LEFT), // Using 6 digits to avoid conflict
            'BOD_Nombre' => $this->faker->city() . ' Warehouse',
            'BOD_Direccion' => $this->faker->address(),
            'BOD_Ciudad' => $this->faker->city(),
            'BOD_Pais' => $this->faker->country(),
            'BOD_CodigoPostal' => $this->faker->postcode(),
            'BOD_Responsable' => $this->faker->name(),
        ];
    }
}
