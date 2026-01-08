<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orden_compras', function (Blueprint $table) {
            $table->string('ORC_Numero', 20)->primary(); // Código tipo ORC001
            $table->string('PRV_Ced_Ruc', 13); // Relación con Proveedor
            $table->date('ORC_Fecha_Emision');
            $table->date('ORC_Fecha_Entrega');
            $table->decimal('ORC_Monto_Total', 10, 2);
            $table->boolean('ORC_Estado')->default(true);
            $table->foreign('PRV_Ced_Ruc')->references('PRV_Ced_Ruc')->on('proveedors')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabla Intermedia: Detalle de la Orden
        Schema::create('detalle_ord_com', function (Blueprint $table) {
            $table->id();
            $table->string('ORC_Numero', 20);
            $table->string('PRO_Codigo', 15);
            $table->integer('cantidad_solicitada');

            $table->foreign('ORC_Numero')->references('ORC_Numero')->on('orden_compras')->onDelete('cascade');
            $table->foreign('PRO_Codigo')->references('PRO_Codigo')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ord_com');
        Schema::dropIfExists('orden_compras');
    }
};
