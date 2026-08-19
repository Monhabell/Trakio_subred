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
        Schema::create('correcciones', function (Blueprint $table) {
            $table->id();
            $table->integer('tipo_cambio');
            $table->integer('id_reg');
            $table->integer('no_pag')->nullable();
            $table->integer('nuevo_numero')->nullable();
            $table->date('nueva_fecha')->nullable();
            $table->unsignedBigInteger('id_receptions');
            $table->foreign('id_receptions')->references('id')->on('receptions')->onDelete('cascade');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->integer('corrected')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('correcciones');
    }
};
