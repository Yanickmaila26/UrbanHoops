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
        Schema::create('datos_facturacion', function (Blueprint $table) {
            $table->string('DAF_Codigo', 15)->unique()->primary();
            $table->string('DAF_CLI_Codigo', 13);
            $table->string('DAF_Direccion');
            $table->string('DAF_Ciudad');
            $table->string('DAF_Estado');
            $table->string('DAF_CP', 10);
            $table->text('DAF_Tarjeta_Numero');
            $table->string('DAF_Tarjeta_Expiracion', 5);
            $table->text('DAF_Tarjeta_CVV');

            $table->timestamps();

            $table->foreign('DAF_CLI_Codigo')->references('CLI_Ced_Ruc')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_facturacion');
    }
};
