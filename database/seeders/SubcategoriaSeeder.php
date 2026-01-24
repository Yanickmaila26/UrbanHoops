<?php

namespace Database\Seeders;

use App\Models\Subcategoria;
use Illuminate\Database\Seeder;

class SubcategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $subcategorias = [
            ['SCT_Codigo' => 'SCT001', 'SCT_Nombre' => 'Zapatillas de competencia', 'CAT_Codigo' => 'CAT001'],
            ['SCT_Codigo' => 'SCT002', 'SCT_Nombre' => 'Camisetas', 'CAT_Codigo' => 'CAT001'],
            ['SCT_Codigo' => 'SCT003', 'SCT_Nombre' => 'Pantalones cortos', 'CAT_Codigo' => 'CAT001'],
            ['SCT_Codigo' => 'SCT004', 'SCT_Nombre' => 'Calcetines', 'CAT_Codigo' => 'CAT001'],
            ['SCT_Codigo' => 'SCT005', 'SCT_Nombre' => 'Sudaderas', 'CAT_Codigo' => 'CAT001'],
            ['SCT_Codigo' => 'SCT006', 'SCT_Nombre' => 'Zapatillas', 'CAT_Codigo' => 'CAT002'],
            ['SCT_Codigo' => 'SCT007', 'SCT_Nombre' => 'Tops', 'CAT_Codigo' => 'CAT002'],
            ['SCT_Codigo' => 'SCT008', 'SCT_Nombre' => 'Leggings', 'CAT_Codigo' => 'CAT002'],
            ['SCT_Codigo' => 'SCT009', 'SCT_Nombre' => 'Shorts', 'CAT_Codigo' => 'CAT002'],
            ['SCT_Codigo' => 'SCT010', 'SCT_Nombre' => 'Zapatillas junior', 'CAT_Codigo' => 'CAT003'],
            ['SCT_Codigo' => 'SCT011', 'SCT_Nombre' => 'Ropa de entrenamiento', 'CAT_Codigo' => 'CAT003'],
            ['SCT_Codigo' => 'SCT012', 'SCT_Nombre' => 'Balones', 'CAT_Codigo' => 'CAT004'],
            ['SCT_Codigo' => 'SCT013', 'SCT_Nombre' => 'Canastas', 'CAT_Codigo' => 'CAT004'],
            ['SCT_Codigo' => 'SCT014', 'SCT_Nombre' => 'Redes', 'CAT_Codigo' => 'CAT004'],
            ['SCT_Codigo' => 'SCT015', 'SCT_Nombre' => 'Marcadores', 'CAT_Codigo' => 'CAT004'],
            ['SCT_Codigo' => 'SCT016', 'SCT_Nombre' => 'Conos y entrenamiento', 'CAT_Codigo' => 'CAT004'],
            ['SCT_Codigo' => 'SCT017', 'SCT_Nombre' => 'Mochilas', 'CAT_Codigo' => 'CAT005'],
            ['SCT_Codigo' => 'SCT018', 'SCT_Nombre' => 'Botellas', 'CAT_Codigo' => 'CAT005'],
            ['SCT_Codigo' => 'SCT019', 'SCT_Nombre' => 'Protecciones (Rodilleras, Tobilleras)', 'CAT_Codigo' => 'CAT005'],
            ['SCT_Codigo' => 'SCT020', 'SCT_Nombre' => 'Cintas para la cabeza', 'CAT_Codigo' => 'CAT005'],
        ];

        foreach ($subcategorias as $sub) {
            Subcategoria::updateOrCreate(['SCT_Codigo' => $sub['SCT_Codigo']], $sub);
        }
    }
}
