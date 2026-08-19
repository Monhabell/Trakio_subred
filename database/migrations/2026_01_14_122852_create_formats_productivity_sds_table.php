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
        Schema::create('formats_productivity_sds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('format_id');
            $table->foreign('format_id')->references('id')->on('formats')->onDelete('cascade');
            $table->unsignedBigInteger('format_sds_id');
            $table->foreign('format_sds_id')->references('id')->on('formats_sds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('formats_productivity_sds');
    }
};
