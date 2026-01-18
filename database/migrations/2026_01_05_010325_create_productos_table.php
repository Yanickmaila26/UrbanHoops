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
        Schema::create('productos', function (Blueprint $table) {
            $table->string('PRO_Codigo', 15)->unique()->primary();
            $table->string('PRV_Ced_Ruc', 13); // Relación con Proveedor
            $table->foreign('PRV_Ced_Ruc')->references('PRV_Ced_Ruc')->on('proveedors')->onDelete('cascade');
            $table->string('PRO_Nombre', 60);
            $table->string('PRO_Descripcion');
            $table->string('PRO_Color', 15);
            $table->string('PRO_Talla', 5);
            $table->string('PRO_Marca', 20);
            $table->decimal('PRO_Precio', 10, 2);
            $table->integer('PRO_Stock')->default(0);
            $table->string('PRO_Imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
