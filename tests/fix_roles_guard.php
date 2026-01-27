<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "--- Fixing ROLE_APP_FRONTEND for client guard ---\n";

try {
    // 1. Check if it exists for 'client'
    $role = Role::where('name', 'ROLE_APP_FRONTEND')->where('guard_name', 'client')->first();

    if ($role) {
        echo "Role already exists for client guard. ID: {$role->id}\n";
    } else {
        echo "Role MISSING for client guard. Creating...\n";
        $role = Role::create(['name' => 'ROLE_APP_FRONTEND', 'guard_name' => 'client']);
        echo "Role created. ID: {$role->id}\n";
    }

    // 2. Also ensure permissions exist if needed (optional for now, just need the role)

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
