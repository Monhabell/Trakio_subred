<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('environment_id');
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('cascade');
            $table->string('label');
            $table->timestamps();

            $table->unique(['environment_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_fields');
    }
};
