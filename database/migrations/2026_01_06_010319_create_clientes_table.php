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
        Schema::create('clientes', function (Blueprint $table) {
            //$table->id();
            $table->string('CLI_Ced_Ruc', 13)->unique()->primary();
            $table->string('CLI_Nombre', 60);
            $table->string('CLI_Telefono', 10);
            $table->string('CLI_Correo', 60)->unique();
            $table->string('CLI_Direccion', 150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
