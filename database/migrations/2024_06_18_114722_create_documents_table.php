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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // id del usuario que cargo el document
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('name'); // mnombre del docuemnto
            $table->string('path');// rurta del archivo
            $table->string('document_mes'); // a que mes pertenece el documento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
