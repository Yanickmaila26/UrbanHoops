<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Bodega;
use App\Models\DatosFacturacion;
use App\Models\OrdenCompra;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;

class MassDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Settings for mass generation
        // Adjust these numbers to reach 2 million total or per entity
        $totalRecords = 1000000; // Example: Set to 2,000,000 for full run (WARNING: Will take a long time)
        $chunkSize = 1000;

        $this->command->info("Starting Mass Data Seeding...");

        // Disable query log to save memory
        DB::disableQueryLog();

        // 1. Clientes
        $this->seedEntity(Cliente::class, $totalRecords, $chunkSize, 'Clientes');

        // 2. Proveedores
        $this->seedEntity(Proveedor::class, $totalRecords, $chunkSize, 'Proveedores');

        // 3. Productos (Bodegas removed as per request)
        // Depends on Proveedores existing
        $this->seedEntity(Producto::class, $totalRecords, $chunkSize, 'Productos');

        // 5. Ordenes de Compra
        $this->seedEntity(OrdenCompra::class, $totalRecords, $chunkSize, 'Ordenes de Compra');

        // 6. Datos Facturación
        $this->seedEntity(DatosFacturacion::class, $totalRecords, $chunkSize, 'Datos Facturación');

        // 7. Facturas
        $this->seedEntity(Factura::class, $totalRecords, $chunkSize, 'Facturas');

        $this->command->info("Mass Data Seeding Completed!");
    }

    private function seedEntity($model, $count, $chunkSize, $label)
    {
        $this->command->info("Seeding $label ($count records)...");
        $progresBar = $this->command->getOutput()->createProgressBar($count);

        $iterations = ceil($count / $chunkSize);

        for ($i = 0; $i < $iterations; $i++) {
            $currentChunk = min($chunkSize, $count - ($i * $chunkSize));

            // Use Factory raw() or create() 
            // create() is slower but handles relations and events. raw() is faster but needs manual inserts.
            // Given the requirement "create factories", standard factory usage is expected.
            // For 2 million, raw arrays + DB::table()->insert() is preferred, but factories allow create()->each().
            // We'll use factory()->create() for correctness, user can optimize if too slow.

            $model::factory()->count($currentChunk)->create();

            $progresBar->advance($currentChunk);

            // Clear memory
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }

        $progresBar->finish();
        $this->command->newLine();
    }
}
