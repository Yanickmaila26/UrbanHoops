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
        Schema::create('producto_bodega', function (Blueprint $table) {
            $table->string('PRO_Codigo', 20);
            $table->string('BOD_Codigo', 10);
            $table->integer('PXB_Stock')->default(0);
            $table->timestamps();

            $table->foreign('PRO_Codigo')->references('PRO_Codigo')->on('productos')->onDelete('cascade');
            $table->foreign('BOD_Codigo')->references('BOD_Codigo')->on('bodegas')->onDelete('cascade');
            $table->unique(['PRO_Codigo', 'BOD_Codigo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_bodega');
    }
};
