<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_record_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('error_record_id');
            $table->foreign('error_record_id')->references('id')->on('error_records')->onDelete('cascade');
            $table->unsignedBigInteger('error_field_id');
            $table->foreign('error_field_id')->references('id')->on('error_fields')->onDelete('cascade');
            $table->string('value')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_record_fields');
    }
};
