<?php

namespace Database\Seeders;

use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'aetherin12@gmail.com'],
            [
                'name' => 'Yanick Maila',
                'password' => Hash::make('secreto123'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'lucho19982005@hotmail.com'],
            [
                'name' => 'Jorge Corrales',
                'password' => Hash::make('secreto123'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'mateopozo240@gmail.com'],
            [
                'name' => 'Mateo Pozo',
                'password' => Hash::make('secreto123'),
            ]
        );
        User::firstOrCreate(
            ['email' => 'estebangarciaojeda@gmail.com'],
            [
                'name' => 'Esteban GarciaS',
                'password' => Hash::make('secreto123'),
            ]
        );
        $tipos = [
            ['TRN_Codigo' => 'T01', 'TRN_Nombre' => 'Compra de Mercadería', 'TRN_Tipo' => 'E'],
            ['TRN_Codigo' => 'T02', 'TRN_Nombre' => 'Devolución al Proveedor', 'TRN_Tipo' => 'S'],
            ['TRN_Codigo' => 'T03', 'TRN_Nombre' => 'Regalo/Promoción', 'TRN_Tipo' => 'S'],
            ['TRN_Codigo' => 'T04', 'TRN_Nombre' => 'Venta de Productos', 'TRN_Tipo' => 'S'],
            ['TRN_Codigo' => 'T05', 'TRN_Nombre' => 'Ajuste de Inventario Positivo', 'TRN_Tipo' => 'E'],
            ['TRN_Codigo' => 'T06', 'TRN_Nombre' => 'Ajuste de Inventario Negativo', 'TRN_Tipo' => 'S'],
            ['TRN_Codigo' => 'T07', 'TRN_Nombre' => 'Orden Cancelada (solo para orden)', 'TRN_Tipo' => 'C'],
        ];

        foreach ($tipos as $tipo) {
            Transaccion::updateOrCreate(
                ['TRN_Codigo' => $tipo['TRN_Codigo']],
                $tipo
            );
        }
        $this->call(RoleSeeder::class);
        $this->call(BodegaSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(ProveedorSeeder::class);
        $this->call(CategoriaSubcategoriaSeeder::class); // Combined seeder for categories and subcategories
        $this->call(ProductoSeeder::class);
    }
}
