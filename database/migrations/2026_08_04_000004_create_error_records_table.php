<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('error_import_id');
            $table->foreign('error_import_id')->references('id')->on('error_imports')->onDelete('cascade');
            $table->unsignedBigInteger('environment_id');
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('cascade');
            $table->unsignedBigInteger('reception_id')->nullable();
            $table->foreign('reception_id')->references('id')->on('receptions')->onDelete('set null');
            $table->string('file_number');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('username_raw')->nullable();
            $table->unsignedInteger('row_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_records');
    }
};
