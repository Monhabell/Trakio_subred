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
        // crear campo de cronicos en la tabla de licencias
        Schema::table('licenses', function (Blueprint $table) {
            $table->boolean('cronicos')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // eliminar campo de cronicos en la tabla de licencias
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropColumn('cronicos');
        });
    }
};
