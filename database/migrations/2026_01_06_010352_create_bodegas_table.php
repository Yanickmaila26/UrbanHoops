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
        Schema::create('bodegas', function (Blueprint $table) {
            $table->string('BOD_Codigo', 15)->unique()->primary();
            $table->string('BOD_Nombre', 30);
            $table->string('BOD_Direccion', 50);
            $table->string('BOD_Ciudad', 30);
            $table->string('BOD_Pais', 30);
            $table->string('BOD_CodigoPostal', 10);
            $table->string('BOD_Responsable', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bodegas');
    }
};
