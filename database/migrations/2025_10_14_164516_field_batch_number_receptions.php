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
        Schema::table('receptions', function (Blueprint $table) {
            $table->integer('batch_number')->nullable()->after('status');
            $table->dropForeign(['user_id_profesional']);
            $table->dropColumn('user_id_profesional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropColumn('batch_number');
            $table->unsignedBigInteger('user_id_profesional')->nullable()->after('status');
            $table->foreign('user_id_profesional')->references('id')->on('users')->onDelete('set null');
        });
    }
};
