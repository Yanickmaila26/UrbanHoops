<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Roles (coincidiendo con DB Roles)
        $roleAdmin = Role::firstOrCreate(['name' => 'ROLE_ADMIN_PROD']);
        $roleGestor = Role::firstOrCreate(['name' => 'ROLE_GESTOR_INV']);
        $roleFrontend = Role::firstOrCreate(['name' => 'ROLE_APP_FRONTEND', 'guard_name' => 'web']);
        $roleFrontendClient = Role::firstOrCreate(['name' => 'ROLE_APP_FRONTEND', 'guard_name' => 'client']);

        // Roles legacy (opcional, mantener si se usan en código viejo, o migrar)
        // Por ahora los mantengo mapeados o creo nuevos para desarrollo.

        // 2. Usuarios de Prueba Específicos

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@urbanhoops.com'],
            [
                'name' => 'Admin Producción',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        // Gestor
        $gestor = User::firstOrCreate(
            ['email' => 'gestor@urbanhoops.com'],
            [
                'name' => 'Gestor Inventario',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $gestor->syncRoles([$roleGestor]);

        // Cliente
        $client = User::firstOrCreate(
            ['email' => 'client@urbanhoops.com'],
            [
                'name' => 'App Frontend User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $client->syncRoles([$roleFrontend]);

        // 3. Asignar Admin a Desenrolladores existentes
        $devs = ['aetherin12@gmail.com', 'lucho19982005@hotmail.com', 'mateopozo240@gmail.com', 'estebangarciaojeda@gmail.com'];
        foreach ($devs as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->assignRole($roleAdmin);
            }
        }
    }
}
