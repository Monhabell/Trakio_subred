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
        Schema::create('reception_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reception_id');
            $table->foreign('reception_id')->references('id')->on('receptions')->onDelete('cascade');
            $table->string('previous_status');
            $table->string('new_status');
            $table->unsignedBigInteger('changed_by');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reception_status_histories');
    }
};
