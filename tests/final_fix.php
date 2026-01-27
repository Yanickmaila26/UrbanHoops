<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\UsuarioAplicacion;

echo "--- FINAL FIX SCRIPT ---\n";

// 1. Ensure Columns Exist in COMEE (Distributed Tables)
$tables = [
    'DETALLE_CARRITO' => 'CRD_TALLA',
    'DETALLE_FACTURA' => 'DFC_TALLA'
];

foreach ($tables as $table => $col) {
    echo "\nCheck/Add $col to $table in COMEE:\n";
    try {
        // Try via LINK
        DB::connection('oracle')->statement("ALTER TABLE u_comee.{$table}@link_comee ADD ({$col} VARCHAR2(50))");
        echo " - Added via LINK.\n";
    } catch (\Exception $e) {
        if (str_contains($e->getMessage(), 'ORA-01430')) {
            echo " - Already exists (via LINK).\n";
        } else {
            echo " - Error (LINK): " . explode("\n", $e->getMessage())[0] . "\n";
            // Try DIRECT
            try {
                if (config('database.connections.oracle_comee')) {
                    DB::connection('oracle_comee')->statement("ALTER TABLE {$table} ADD ({$col} VARCHAR2(50))");
                    echo " - Added via DIRECT connection.\n";
                }
            } catch (\Exception $e2) {
                if (str_contains($e2->getMessage(), 'ORA-01430')) {
                    echo " - Already exists (via DIRECT).\n";
                } else {
                    echo " - Error (DIRECT): " . explode("\n", $e2->getMessage())[0] . "\n";
                }
            }
        }
    }
}

// 2. Ensure Columns Exist in PROD (Just in case referenced)
// Note: Distributed tables should physically exist in COMEE. 
// If PROD has synonyms, they point to COMEE.
// If PROD has physical tables (incorrect), they might be missing cols.
// We'll skip PROD alter for distributed tables if we assume strict distribution.

// 3. Fix Roles for aetherin12@gmail.com
echo "\nFixing Roles for aetherin12@gmail.com:\n";
$email = 'aetherin12@gmail.com';
$user = UsuarioAplicacion::where('email', $email)->first();

if ($user) {
    echo " - User found: {$user->id}\n";

    // Ensure Role exists for CLIENT guard
    $roleName = 'ROLE_APP_FRONTEND';
    $guard = 'client';

    $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();
    if (!$role) {
        $role = Role::create(['name' => $roleName, 'guard_name' => $guard]);
        echo " - Created Role: $roleName ($guard)\n";
    }

    // Assign Role manually to model_has_roles to avoid ambiguity
    $exists = DB::table('model_has_roles')
        ->where('role_id', $role->id)
        ->where('model_type', get_class($user))
        ->where('model_id', $user->id)
        ->exists();

    if (!$exists) {
        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => get_class($user),
            'model_id' => $user->id
        ]);
        echo " - Assigned $roleName to user.\n";
    } else {
        echo " - User already has Valid Role.\n";
    }
} else {
    echo " - User $email NOT FOUND. Run db:seed first.\n";
}

// 4. Fix PXB_Stock
echo "\nFixing Stock (PXB_Stock = PRO_Stock):\n";
// Re-run the logic from fix_bodega_stock.php inline or call it
$products = \App\Models\Producto::all();
$bodega = \App\Models\Bodega::first();
if ($bodega) {
    foreach ($products as $prod) {
        DB::table('producto_bodega')
            ->where('PRO_Codigo', $prod->PRO_Codigo)
            ->where('BOD_Codigo', $bodega->BOD_Codigo)
            ->update(['PXB_Stock' => $prod->PRO_Stock]);
    }
    echo " - Stock synced.\n";
}

echo "\n--- FINAL FIX COMPLETED ---\n";
