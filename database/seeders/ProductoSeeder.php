<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'PRO_Codigo' => 'P001',
                'PRO_Nombre' => 'Air Jordan 1 Retro',
                'PRO_Descripcion' => 'Calzado clasico de baloncesto edicion limitada',
                'PRO_Marca' => 'Nike',
                'PRO_Color' => 'Rojo Blanco',
                'PRO_Talla' => '42',
                'PRO_Precio' => 185.00,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P002',
                'PRO_Nombre' => 'Ultraboost 22',
                'PRO_Descripcion' => 'Zapato de running con maxima amortiguacion',
                'PRO_Marca' => 'Adidas',
                'PRO_Color' => 'Negro',
                'PRO_Talla' => '40',
                'PRO_Precio' => 160.00,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P003',
                'PRO_Nombre' => 'Classic Leather',
                'PRO_Descripcion' => 'Estilo retro casual en cuero autentico',
                'PRO_Marca' => 'Reebok',
                'PRO_Color' => 'Blanco',
                'PRO_Talla' => '38',
                'PRO_Precio' => 85.00,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P004',
                'PRO_Nombre' => 'Old Skool',
                'PRO_Descripcion' => 'Zapato de lona clasico para skate',
                'PRO_Marca' => 'Vans',
                'PRO_Color' => 'Negro Blanco',
                'PRO_Talla' => '39',
                'PRO_Precio' => 75.00,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P005',
                'PRO_Nombre' => 'Air Max 270',
                'PRO_Descripcion' => 'Unidad de aire visible para mayor confort',
                'PRO_Marca' => 'Nike',
                'PRO_Color' => 'Azul',
                'PRO_Talla' => '41',
                'PRO_Precio' => 155.50,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P006',
                'PRO_Nombre' => 'Forum Low',
                'PRO_Descripcion' => 'Inspirado en el basket de los años 80',
                'PRO_Marca' => 'Adidas',
                'PRO_Color' => 'Blanco Azul',
                'PRO_Talla' => '43',
                'PRO_Precio' => 110.00,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P007',
                'PRO_Nombre' => 'Chuck Taylor All Star',
                'PRO_Descripcion' => 'El zapato de lona mas iconico del mundo',
                'PRO_Marca' => 'Converse',
                'PRO_Color' => 'Negro',
                'PRO_Talla' => '37',
                'PRO_Precio' => 65.00,
                'PRO_Stock' => 0
            ],
            [
                'PRO_Codigo' => 'P008',
                'PRO_Nombre' => 'RSX Bold',
                'PRO_Descripcion' => 'Silueta voluminosa con tecnologia retro',
                'PRO_Marca' => 'Puma',
                'PRO_Color' => 'Multicolor',
                'PRO_Talla' => '42',
                'PRO_Precio' => 125.00,
                'PRO_Stock' => 0
            ],
        ];

        foreach ($productos as $prod) {
            Producto::createProducto($prod);
        }
    }
}
