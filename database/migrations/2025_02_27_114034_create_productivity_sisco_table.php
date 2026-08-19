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
        Schema::create('productivity_siscos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('environment_id');
            $table->foreign('environment_id')->references('id')->on('environments');
            $table->unsignedBigInteger('typed_by')->nullable();
            $table->foreign('typed_by')->references('id')->on('users');
            $table->unsignedBigInteger('package_id');
            $table->foreign('package_id')->references('id')->on('packages');
            $table->unsignedBigInteger('format_id');
            $table->foreign('format_id')->references('id')->on('formats');
            $table->string('sisco_code')->nullable();
            $table->integer('count_users')->nullable();
            $table->date('typed_date')->nullable();
            $table->date('devolution_date')->nullable();
            $table->text('observations')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->foreign('received_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productivity_sisco');
    }
};
