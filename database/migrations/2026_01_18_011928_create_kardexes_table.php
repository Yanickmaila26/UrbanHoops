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
        Schema::create('kardexes', function (Blueprint $table) {
            //$table->id();
            $table->string('KAR_Codigo', 15)->unique()->primary();
            $table->string('BOD_Codigo', 15);
            $table->foreign('BOD_Codigo')->references('BOD_Codigo')->on('bodegas')->onDelete('cascade');
            $table->string('TRN_Codigo', 15);
            $table->foreign('TRN_Codigo')->references('TRN_Codigo')->on('transaccions')->onDelete('cascade');
            $table->string('ORC_Numero', 15)->nullable();
            $table->foreign('ORC_Numero')->references('ORC_Numero')->on('orden_compras')->onDelete('cascade'); // Assuming table is orden_compras from previous context (step 358 used OrdenCompra model)
            $table->string('FAC_Codigo', 15)->nullable();
            $table->foreign('FAC_Codigo')->references('FAC_Codigo')->on('facturas')->onDelete('cascade');
            $table->string('PRO_Codigo', 20)->nullable(); // Changed to 20 to match Products table if needed, usually 15 or 20. Step 259 used 20.
            $table->foreign('PRO_Codigo')->references('PRO_Codigo')->on('productos')->onDelete('cascade');
            $table->integer('KAR_cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardexes');
    }
};
