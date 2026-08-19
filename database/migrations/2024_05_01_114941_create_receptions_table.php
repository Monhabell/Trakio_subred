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
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('environment');
            $table->foreign('environment')->references('id')->on('environments')->onDelete('cascade');
            $table->string('file_number');
            $table->unsignedBigInteger('format_id');
            $table->foreign('format_id')->references('id')->on('formats')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->unsignedBigInteger('delivered_by');
            $table->foreign('delivered_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('received_by');
            $table->foreign('received_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('package_id');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->unsignedBigInteger('typed_by');
            $table->foreign('typed_by')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('typed_date');
            $table->integer('type');
            $table->dateTime('return_date');
            $table->unsignedBigInteger('returned_by');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_receptions');
    }
};
