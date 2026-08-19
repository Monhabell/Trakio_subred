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
        // crear nuevo campo concatenado consecutivo
        Schema::table('reception_sisco', function (Blueprint $table) {
            $table->integer('consecutivo_sisco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reception_sisco', function (Blueprint $table) {
            $table->dropColumn('consecutivo_sisco');
        });
    }
};
