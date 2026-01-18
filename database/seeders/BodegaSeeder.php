<?php

namespace Database\Seeders;

use App\Models\Bodega;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BodegaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bodega::create([
            'BOD_Codigo' => 'BO001',
            'BOD_Nombre' => 'Bodega Central',
            'BOD_Direccion' => 'Av. Principal 123',
            'BOD_Ciudad' => 'Quito',
            'BOD_Pais' => 'Ecuador',
            'BOD_CodigoPostal' => '170101',
            'BOD_Responsable' => 'Admin'
        ]);
        Bodega::create([
            'BOD_Codigo' => 'BO002',
            'BOD_Nombre' => 'Bodega Sur',
            'BOD_Direccion' => 'Av. Sur 123',
            'BOD_Ciudad' => 'Quito',
            'BOD_Pais' => 'Ecuador',
            'BOD_CodigoPostal' => '170102',
            'BOD_Responsable' => 'Admin'
        ]);
        Bodega::create([
            'BOD_Codigo' => 'BO003',
            'BOD_Nombre' => 'Bodega Norte',
            'BOD_Direccion' => 'Av. Norte 123',
            'BOD_Ciudad' => 'Quito',
            'BOD_Pais' => 'Ecuador',
            'BOD_CodigoPostal' => '170103',
            'BOD_Responsable' => 'Admin'
        ]);
        Bodega::create([
            'BOD_Codigo' => 'BO004',
            'BOD_Nombre' => 'Bodega Este',
            'BOD_Direccion' => 'Av. Este 123',
            'BOD_Ciudad' => 'Quito',
            'BOD_Pais' => 'Ecuador',
            'BOD_CodigoPostal' => '170104',
            'BOD_Responsable' => 'Admin'
        ]);
        Bodega::create([
            'BOD_Codigo' => 'BO005',
            'BOD_Nombre' => 'Bodega Oeste',
            'BOD_Direccion' => 'Av. Oeste 123',
            'BOD_Ciudad' => 'Quito',
            'BOD_Pais' => 'Ecuador',
            'BOD_CodigoPostal' => '170105',
            'BOD_Responsable' => 'Admin'
        ]);
    }
}
