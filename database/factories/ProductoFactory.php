<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Subcategoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $tallaOptions = ['S', 'M', 'L', 'XL', 'XXL'];
        $tallas = [];
        // Generate random stock per size
        foreach ($tallaOptions as $talla) {
            if ($this->faker->boolean(70)) { // 70% chance to have this size
                $tallas[] = [
                    'talla' => $talla,
                    'stock' => $this->faker->numberBetween(1, 100)
                ];
            }
        }
        // Ensure at least one size
        if (empty($tallas)) {
            $tallas[] = ['talla' => 'M', 'stock' => 10];
        }

        return [
            'PRO_Codigo' => 'PRO-' . $this->faker->unique()->numerify('##########'),
            // Ideally pick existing IDs to handle large scale without creating new ones every time
            'PRV_Ced_Ruc' => Proveedor::inRandomOrder()->first()?->PRV_Ced_Ruc ?? Proveedor::factory(),
            'SCT_Codigo' => Subcategoria::inRandomOrder()->first()?->SCT_Codigo ?? null,
            'PRO_Nombre' => $this->faker->words(3, true),
            'PRO_Descripcion' => $this->faker->sentence(),
            'PRO_Color' => $this->faker->colorName(),
            'PRO_Talla' => $tallas, // Casts to array/json automatically in model
            'PRO_Marca' => $this->faker->word(),
            'PRO_Precio' => $this->faker->randomFloat(2, 10, 500),
            'PRO_Stock' => array_sum(array_column($tallas, 'stock')),
            'PRO_Imagen' => null, // Or a placeholder URL
            'activo' => true,
        ];
    }
}
