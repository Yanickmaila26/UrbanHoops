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
        Schema::create('subcategorias', function (Blueprint $table) {
            $table->string('SCT_Codigo', 6)->primary();
            $table->string('CAT_Codigo', 6);
            $table->foreign('CAT_Codigo')->references('CAT_Codigo')->on('categorias')->onDelete('cascade');
            $table->string('SCT_Nombre', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategorias');
    }
};
