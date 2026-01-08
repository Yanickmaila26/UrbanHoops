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
        Schema::create('proveedors', function (Blueprint $table) {
            //$table->id();
            $table->string('PRV_Ced_Ruc', 13)->unique()->primary();
            $table->string('PRV_Nombre', 50);
            $table->string('PRV_Direccion', 150);
            $table->string('PRV_Telefono', 10);
            $table->string('PRV_Correo', 60);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
