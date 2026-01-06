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
            //$table->id();
            $table->string('PRO_Codigo', 15)->unique()->primary();
            $table->string('PRO_Nombre', 60);
            $table->string('PRO_Descripcion_Corta', 100);
            $table->text('PRO_Descripcion_Larga');

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
