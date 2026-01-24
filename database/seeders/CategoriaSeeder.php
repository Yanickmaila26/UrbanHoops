<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['CAT_Codigo' => 'CAT001', 'CAT_Nombre' => 'Hombre'],
            ['CAT_Codigo' => 'CAT002', 'CAT_Nombre' => 'Mujer'],
            ['CAT_Codigo' => 'CAT003', 'CAT_Nombre' => 'Niños'],
            ['CAT_Codigo' => 'CAT004', 'CAT_Nombre' => 'Entrenamiento y Juego'],
            ['CAT_Codigo' => 'CAT005', 'CAT_Nombre' => 'Accesorios'],
        ];

        foreach ($categorias as $cat) {
            Categoria::updateOrCreate(['CAT_Codigo' => $cat['CAT_Codigo']], $cat);
        }
    }
}
