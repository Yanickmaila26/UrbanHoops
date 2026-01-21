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
        Schema::table('productos', function (Blueprint $table) {
            $table->string('SCT_Codigo', 10)->nullable()->after('PRO_Codigo'); // Placing it after PRO_Codigo
            $table->foreign('SCT_Codigo')->references('SCT_Codigo')->on('subcategorias')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['SCT_Codigo']);
            $table->dropColumn('SCT_Codigo');
        });
    }
};
