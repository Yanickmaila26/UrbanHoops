<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Producto;

echo "--- Fixing PXB_Stock in producto_bodega ---\n";

try {
    $products = Producto::all();
    $bodega = \App\Models\Bodega::first();

    if (!$bodega) {
        echo "No bodega found. Creating default...\n";
        $bodega = \App\Models\Bodega::create([
            'BOD_Nombre' => 'Bodega Central',
            'BOD_Direccion' => 'Principal',
            'BOD_Ciudad' => 'Quito',
            'BOD_Pais' => 'Ecuador',
            'BOD_CodigoPostal' => '170505',
            'BOD_Responsable' => 'Sistema'
        ]);
    }

    $bodegaId = $bodega->BOD_Codigo;
    echo "Using Bodega ID: $bodegaId\n";

    $count = 0;
    foreach ($products as $prod) {
        // Update PXB_Stock matching PRO_Stock
        $affected = DB::table('producto_bodega')
            ->where('PRO_Codigo', $prod->PRO_Codigo)
            ->where('BOD_Codigo', $bodegaId)
            ->update(['PXB_Stock' => $prod->PRO_Stock]);

        if ($affected) {
            $count++;
        } else {
            // Insert if missing
            $exists = DB::table('producto_bodega')
                ->where('PRO_Codigo', $prod->PRO_Codigo)
                ->where('BOD_Codigo', $bodegaId)
                ->exists();

            if (!$exists) {
                DB::table('producto_bodega')->insert([
                    'PRO_Codigo' => $prod->PRO_Codigo,
                    'BOD_Codigo' => $bodegaId,
                    'PXB_Stock' => $prod->PRO_Stock,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $count++; // Count inserts too
                echo "Inserted missing stock for {$prod->PRO_Nombre}\n";
            }
        }
    }

    echo "Updated/Inserted stock for $count products.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
