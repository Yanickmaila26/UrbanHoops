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

        // Crear Roles
        $roleAdmin = Role::create(['name' => 'Administrador']);
        $roleBodega = Role::create(['name' => 'Bodega']);
        $roleVentas = Role::create(['name' => 'Ventas']);
        $roleComercial = Role::create(['name' => 'Comercial']);
        $roleFinanzas = Role::create(['name' => 'Finanzas']);

        // Asignar rol de Administrador a usuarios existentes (por defecto para desarrollo)
        $users = User::all();
        foreach ($users as $user) {
            // Asumiendo que los primeros usuarios creados son Administradores
            $user->assignRole($roleAdmin);
        }
    }
}
