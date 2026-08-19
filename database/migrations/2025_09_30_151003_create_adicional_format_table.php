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
        Schema::create('adicional_format', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_format');
            $table->foreign('id_format')->references('id')->on('formats');
            $table->unsignedBigInteger('id_format_adicional')->nullable();
            $table->foreign('id_format_adicional')->references('id')->on('formats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adicional_format');
    }
};
