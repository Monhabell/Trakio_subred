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
            $table->unsignedBigInteger('localidad_id')->after('environment')->nullable();
            $table->foreign('localidad_id')->references('id')->on('localidades')->onDelete('cascade');

            $table->unsignedBigInteger('user_id_profesional')->after('localidad_id')->nullable();
            $table->foreign('user_id_profesional')->references('id')->on('users')->onDelete('cascade');

            $table->date('stamp_date')->nullable()->after('user_id_profesional');
            
            $table->unsignedBigInteger('user_id_stamp')->after('stamp_date')->nullable(); // Usuario que sella la ficha
            $table->foreign('user_id_stamp')->references('id')->on('users')->onDelete('cascade');

            $table->dateTime('required_date')->nullable()->after('user_id_stamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropForeign(['localidad_id']);
            $table->dropColumn('localidad_id');
            $table->dropForeign(['user_id_profesional']);
            $table->dropColumn('user_id_profesional');
            $table->dropColumn('stamp_date');
            $table->dropForeign(['user_id_stamp']);
            $table->dropColumn('user_id_stamp');
            $table->dropColumn('required_date');
        });
    }
};
