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
        Schema::create('transaccions', function (Blueprint $table) {
            // $table->id();
            $table->string('TRN_Codigo', 15)->unique()->primary();
            $table->string('TRN_Nombre', 50);
            $table->char('TRN_Tipo', 1); // 'E' para ingreso, 'S' para egreso
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaccions');
    }
};
