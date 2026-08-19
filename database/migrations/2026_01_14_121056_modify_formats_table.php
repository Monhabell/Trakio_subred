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
        Schema::table('formats', function (Blueprint $table) {
            // añadir campó boleano 'is_active'
            $table->boolean('is_active')->default(true);
            // eliminar columna sds_id si existe
            if (Schema::hasColumn('formats', 'sds_id')) {
                $table->dropColumn('sds_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formats', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->string('sds_id')->nullable();
        });
    }
};
