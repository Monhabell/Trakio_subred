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
        Schema::create('productivity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dig_id');
            $table->foreign('dig_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('file_id');
            $table->foreign('file_id')->references('id')->on('receptions')->onDelete('cascade');
            $table->unsignedBigInteger('base_id');
            $table->foreign('base_id')->references('id')->on('formats')->onDelete('cascade');
            $table->text('observations')->nullable();
            $table->integer('quantity_users')->default(0);
            $table->integer('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productivity');
    }
};
