<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "--- Debugging Role Existence ---\n";

$roleName = 'ROLE_APP_FRONTEND';
$guardName = 'client';

try {
    echo "Attempting to find role: '$roleName' for guard: '$guardName'\n";
    $role = Role::findByName($roleName, $guardName);
    echo "SUCCESS: Found Role ID: " . $role->id . "\n";
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}

echo "\n--- Inspecting Database (roles table) ---\n";
$roles = DB::table('roles')->get();
foreach ($roles as $r) {
    echo "ID: $r->id | Name: '$r->name' | Guard: '$r->guard_name'\n";
}

echo "\n--- Inspecting Cache ---\n";
// Spatie cache key usually involves config
$cacheKey = config('permission.cache.key');
echo "Cache Key: $cacheKey\n";
// We can't easily dump the cache content, but we can clear it again
app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
echo "Cache cleared.\n";
