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
        Schema::create('detalle_carrito', function (Blueprint $table) {
            $table->string('CRC_Carrito', 13);
            $table->foreign('CRC_Carrito')->references('CRC_Carrito')->on('carritos')->onDelete('cascade');
            $table->string('PRO_Codigo', 20);
            $table->foreign('PRO_Codigo')->references('PRO_Codigo')->on('productos')->onDelete('cascade');
            $table->integer('CRD_Cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_carrito');
    }
};
