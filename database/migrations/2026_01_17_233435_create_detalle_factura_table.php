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
        Schema::create('detalle_factura', function (Blueprint $table) {
            //$table->id();
            $table->string('FAC_Codigo', 15);
            $table->foreign('FAC_Codigo')->references('FAC_Codigo')->on('facturas')->onDelete('cascade');
            $table->string('PRO_Codigo', 20);
            $table->foreign('PRO_Codigo')->references('PRO_Codigo')->on('productos')->onDelete('cascade');
            $table->integer('DFC_Cantidad');
            $table->decimal('DFC_Precio', 10, 2);
            $table->string('DFC_Talla', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_factura');
    }
};
