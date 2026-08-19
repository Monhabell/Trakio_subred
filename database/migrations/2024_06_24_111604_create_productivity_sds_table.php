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
        Schema::create('productivity_sds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sds')->nullable();
            //$table->foreign('id_sds')->references('id_sds')->on('formats')->onDelete('cascade');
            $table->unsignedBigInteger('no_ficha')->nullable();
            //$table->foreign('no_ficha')->references('file_number')->on('receptions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productivity_sds');
    }
};
