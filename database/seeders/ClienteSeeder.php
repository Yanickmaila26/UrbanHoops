<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'CLI_Ced_Ruc' => '1712345678',
                'CLI_Nombre' => 'Juan Perez',
                'CLI_Telefono' => '0991234567',
                'CLI_Correo' => 'juan.perez@email.com',
            ],
            [
                'CLI_Ced_Ruc' => '1798765432001',
                'CLI_Nombre' => 'Empresa XYZ S.A.',
                'CLI_Telefono' => '022345678',
                'CLI_Correo' => 'contacto@xyz.com',
            ],
            [
                'CLI_Ced_Ruc' => '1700112233',
                'CLI_Nombre' => 'Maria Rodriguez',
                'CLI_Telefono' => '0987654321',
                'CLI_Correo' => 'maria.rodriguez@email.com',
            ],
            [
                'CLI_Ced_Ruc' => '0911223344',
                'CLI_Nombre' => 'Carlos Lopez',
                'CLI_Telefono' => '0998877665',
                'CLI_Correo' => 'carlos.lopez@email.com',
            ],
            [
                'CLI_Ced_Ruc' => '0102030405',
                'CLI_Nombre' => 'Ana Gomez',
                'CLI_Telefono' => '0955443322',
                'CLI_Correo' => 'ana.gomez@email.com',
            ],
            [
                'CLI_Ced_Ruc' => '1002003001',
                'CLI_Nombre' => 'Tech Solutions Ltd',
                'CLI_Telefono' => '062998877',
                'CLI_Correo' => 'info@techsolutions.com',
            ],
            [
                'CLI_Ced_Ruc' => '1715566778',
                'CLI_Nombre' => 'Luis Torres',
                'CLI_Telefono' => '0991122334',
                'CLI_Correo' => 'luis.torres@email.com',
            ],
            [
                'CLI_Ced_Ruc' => '1755664433',
                'CLI_Nombre' => 'Sofia Diaz',
                'CLI_Telefono' => '0981122445',
                'CLI_Correo' => 'sofia.diaz@email.com',
            ]
        ];

        // Insertar o ignorar si ya existen para evitar duplicados en PK
        foreach ($clientes as $cliente) {
            DB::table('clientes')->updateOrInsert(
                ['CLI_Ced_Ruc' => $cliente['CLI_Ced_Ruc']],
                $cliente
            );
        }

        // Crear Cliente Usuario Específico (Yanick)
        $yanickEmail = 'aetherin12@gmail.com';

        // 1. Crear UsuarioAplicacion si no existe
        $user = \App\Models\UsuarioAplicacion::firstOrCreate(
            ['email' => $yanickEmail],
            ['password' => \Illuminate\Support\Facades\Hash::make('secreto123')]
        );

        // 2. Crear Cliente asociado
        \App\Models\Cliente::updateOrCreate(
            ['CLI_Correo' => $yanickEmail],
            [
                'CLI_Ced_Ruc' => '1712294568', // Cédula ficticia para el ejemplo
                'CLI_Nombre' => 'Yanick Maila',
                'CLI_Telefono' => '0990339510',
                'usuario_aplicacion_id' => $user->id
            ]
        );
    }
}
