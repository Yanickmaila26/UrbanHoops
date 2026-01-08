<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'PRV_Ced_Ruc' => '1792345678001',
                'PRV_Nombre' => 'Distribuidora SportLevel S.A.',
                'PRV_Direccion' => 'Av. Galo Plaza Lasso y de los Jazmines',
                'PRV_Telefono' => '0224567890',
                'PRV_Correo' => 'ventas@sportlevel.com'
            ],
            [
                'PRV_Ced_Ruc' => '0991234567001',
                'PRV_Nombre' => 'Importadora UrbanStyle',
                'PRV_Direccion' => 'Parque Industrial California, Bodega 4',
                'PRV_Telefono' => '0428901234',
                'PRV_Correo' => 'contacto@urbanstyle.ec'
            ],
            [
                'PRV_Ced_Ruc' => '1890011223001',
                'PRV_Nombre' => 'Calzado del Pacifico CIA Ltda',
                'PRV_Direccion' => 'Calle Bolivar y Espejo, Ambato',
                'PRV_Telefono' => '0328245560',
                'PRV_Correo' => 'gerencia@calzadopacifico.com'
            ],
            [
                'PRV_Ced_Ruc' => '1798887776001',
                'PRV_Nombre' => 'Elite Sneakers Ecuador',
                'PRV_Direccion' => 'C.C. Iñaquito, Local 15',
                'PRV_Telefono' => '0987654321',
                'PRV_Correo' => 'pedidos@elitesneakers.com'
            ],
            [
                'PRV_Ced_Ruc' => '0102030405001',
                'PRV_Nombre' => 'Logistica TransAndina',
                'PRV_Direccion' => 'Av. Remigio Crespo, Cuenca',
                'PRV_Telefono' => '0741022330',
                'PRV_Correo' => 'info@transandina.com'
            ],
        ];

        foreach ($proveedores as $prov) {
            Proveedor::createProveedor($prov);
        }
    }
}
