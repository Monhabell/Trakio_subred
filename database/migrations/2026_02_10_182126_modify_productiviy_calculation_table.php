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
        Schema::table('productivity_calculation', function (Blueprint $table) {
            $table->unsignedBigInteger('dig_id')->nullable()->after('id');
            $table->foreign('dig_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productivity_calculation', function (Blueprint $table) {
            $table->dropForeign(['dig_id']);
            $table->dropColumn('dig_id');
        });
    }
};
