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
        Schema::table('pedidos', function (Blueprint $table) {
            // Fix: Add PED_FAC_Codigo if missing
            if (!Schema::hasColumn('pedidos', 'PED_FAC_Codigo')) {
                $table->string('PED_FAC_Codigo', 15)->nullable()->after('PED_DAF_Codigo');
                $table->foreign('PED_FAC_Codigo')->references('FAC_Codigo')->on('facturas')->onDelete('cascade');
            }

            // Fix: Drop PED_Total if present (it should have been dropped)
            if (Schema::hasColumn('pedidos', 'PED_Total')) {
                $table->dropColumn('PED_Total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No strict down needed as this is a repair, but usually we reverse changes
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'PED_FAC_Codigo')) {
                $table->dropForeign(['PED_FAC_Codigo']);
                $table->dropColumn('PED_FAC_Codigo');
            }
            if (!Schema::hasColumn('pedidos', 'PED_Total')) {
                $table->decimal('PED_Total', 10, 2)->nullable();
            }
        });
    }
};
