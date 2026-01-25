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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->string('PED_Codigo', 15)->unique()->primary();
            $table->string('PED_CLI_Codigo', 13);
            $table->string('PED_DAF_Codigo', 15); // Billing/Shipping Data

            $table->dateTime('PED_Fecha');
            $table->enum('PED_Estado', ['Pendiente', 'Procesando', 'Enviado', 'Entregado'])->default('Pendiente');
            $table->decimal('PED_Total', 10, 2);

            $table->timestamps();

            $table->foreign('PED_CLI_Codigo')->references('CLI_Ced_Ruc')->on('clientes')->onDelete('cascade');
            $table->foreign('PED_DAF_Codigo')->references('DAF_Codigo')->on('datos_facturacion')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
