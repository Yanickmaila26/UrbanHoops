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

        // 2. Modify Pedidos table
        Schema::table('pedidos', function (Blueprint $table) {
            // Remove Total as it is in Factura
            $table->dropColumn('PED_Total');

            // Add FK to Facturas
            $table->string('PED_FAC_Codigo', 15)->after('PED_DAF_Codigo');
            $table->foreign('PED_FAC_Codigo')->references('FAC_Codigo')->on('facturas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['PED_FAC_Codigo']);
            $table->dropColumn('PED_FAC_Codigo');
            $table->decimal('PED_Total', 10, 2);
        });
    }
};
