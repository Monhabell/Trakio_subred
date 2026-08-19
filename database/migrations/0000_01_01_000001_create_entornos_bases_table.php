<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
         //Tabla entornos
         Schema::create('environments', function (Blueprint $table) {
            $table->id();
            $table->string('entorno')->unique();
        });

        // Tabla 'bases'
        Schema::create('formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('header_time');
            $table->integer('body_time');
            $table->unsignedBigInteger('environment_id');
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('formats');
        Schema::dropIfExists('environments');              
    }
};

