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
        Schema::create('facturas', function (Blueprint $table) {
            //$table->id();
            $table->string('FAC_Codigo', 15)->unique()->primary();
            $table->string('CLI_Ced_Ruc', 13);
            $table->foreign('CLI_Ced_Ruc')->references('CLI_Ced_Ruc')->on('clientes')->onDelete('cascade');
            $table->integer('FAC_IVA');
            $table->decimal('FAC_IVA_Porcentaje', 5, 2)->default(15.00);
            $table->decimal('FAC_Subtotal', 10, 2);
            $table->decimal('FAC_Total', 10, 2);
            $table->enum('FAC_Estado', ['Pen', 'Pag', 'Anu'])->default('Pen')->comment('Pen: Pendiente, Pag: Pagada, Anu: Anulada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
