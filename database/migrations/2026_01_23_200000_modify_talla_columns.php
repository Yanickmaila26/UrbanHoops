<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modify PRO_Talla in productos table
        // We drop the column and recreate it to ensure type change to JSON/Text works smoothly across drivers
        // In Oracle/Generic, changing string to json might require explicit casting, easiest is drop/add for dev
        // or change() if supported. Given current state, we'll try change() first or raw statement.
        // For Oracle compat in Laravel, ->json() usually maps effectively.

        // Safety: data might be lost if we don't migrate it, but plan says "reset or converted".
        // We will just drop and add for simplicity in this dev phase, or change.
        Schema::table('productos', function (Blueprint $table) {
            // Drop existing string column
            $table->dropColumn('PRO_Talla');
        });

        Schema::table('productos', function (Blueprint $table) {
            // Re-add as JSON (or long text for wider compatibility if JSON type issues arise, but JSON is goal)
            $table->json('PRO_Talla'); // Stores [{"talla": "M", "stock": 10}, ...]
        });

        // 2. Add size columns to detail tables
        Schema::table('detalle_carrito', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_carrito', 'CRD_Talla')) {
                $table->string('CRD_Talla', 20)->nullable();
            }
        });

        Schema::table('detalle_ord_com', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_ord_com', 'DOC_Talla')) {
                $table->string('DOC_Talla', 20)->nullable();
            }
        });

        Schema::table('detalle_factura', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_factura', 'DFC_Talla')) {
                $table->string('DFC_Talla', 20)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_factura', function (Blueprint $table) {
            $table->dropColumn('DFC_Talla');
        });

        Schema::table('detalle_ord_com', function (Blueprint $table) {
            $table->dropColumn('DOC_Talla');
        });

        Schema::table('detalle_carrito', function (Blueprint $table) {
            $table->dropColumn('CRD_Talla');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('PRO_Talla');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('PRO_Talla', 50);
        });
    }
};
