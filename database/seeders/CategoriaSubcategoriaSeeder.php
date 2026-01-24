<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Subcategoria;

class CategoriaSubcategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Structure provided: Family (Combined) -> Category -> Subcategory List
        $data = [
            'Ropa y Calzado' => [ // Family - Ignored
                'Hombre' => [
                    'Zapatillas de competencia',
                    'Camisetas',
                    'Pantalones cortos',
                    'Calcetines',
                    'Sudaderas'
                ],
                'Mujer' => [
                    'Zapatillas',
                    'Tops',
                    'Leggings',
                    'Shorts'
                ],
                'Niños' => [
                    'Zapatillas junior',
                    'Ropa de entrenamiento'
                ]
            ],
            'Equipamiento' => [ // Family - Ignored
                'Entrenamiento y Juego' => [
                    'Balones',
                    'Canastas',
                    'Redes',
                    'Marcadores',
                    'Conos y entrenamiento'
                ],
                'Accesorios' => [
                    'Mochilas',
                    'Botellas',
                    'Protecciones (Rodilleras, Tobilleras)',
                    'Cintas para la cabeza'
                ]
            ]
        ];

        foreach ($data as $family => $categories) {
            foreach ($categories as $categoryName => $subcategories) {
                // Create or find Category
                $categoria = Categoria::firstOrCreate(
                    ['CAT_Nombre' => $categoryName],
                    ['CAT_Codigo' => Categoria::generateId()]
                );

                foreach ($subcategories as $subcategoryName) {
                    // Create Subcategory
                    Subcategoria::firstOrCreate(
                        [
                            'SCT_Nombre' => $subcategoryName,
                            'CAT_Codigo' => $categoria->CAT_Codigo
                        ],
                        ['SCT_Codigo' => Subcategoria::generateId()]
                    );
                }
            }
        }
    }
}
