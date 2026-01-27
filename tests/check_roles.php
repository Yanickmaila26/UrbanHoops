<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UsuarioAplicacion;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "--- Checking Roles for Client User ---\n";

try {
    // 1. Check if Role exists
    $role = Role::where('name', 'ROLE_APP_FRONTEND')->where('guard_name', 'client')->first();
    echo "Role 'ROLE_APP_FRONTEND' (guard: client): " . ($role ? "EXISTS (ID: {$role->id})" : "MISSING") . "\n";

    // 2. Check User
    $user = UsuarioAplicacion::first();
    if (!$user) {
        echo "No UsuarioAplicacion found.\n";
        exit;
    }

    echo "User found: ID {$user->id}, Email: {$user->email}\n";
    // echo "Current Roles: " . $user->getRoleNames() . "\n";

    // 3. Force reload relations to be safe
    $user->load('roles');

    $hasRole = $user->hasRole('ROLE_APP_FRONTEND', 'client');
    echo "Has ROLE_APP_FRONTEND (guard: client)? " . ($hasRole ? "YES" : "NO") . "\n";

    if (!$hasRole && $role) {
        echo "Assigning role...\n";
        $user->assignRole($role);
        echo "Role assigned.\n";

        // Cache reset just in case
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $user->load('roles');
        $hasRole = $user->hasRole('ROLE_APP_FRONTEND', 'client');
        echo "Re-check after assignment: " . ($hasRole ? "YES" : "NO") . "\n";
    }

    echo "--- Database Check ---\n";
    $roles = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', get_class($user))
        ->get();

    foreach ($roles as $r) {
        echo " - Role ID: {$r->role_id}, Model: {$r->model_type}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
