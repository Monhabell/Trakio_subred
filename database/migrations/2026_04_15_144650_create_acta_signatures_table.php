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
        Schema::create('acta_signatures', function (Blueprint $table) {
            $table->id();
            // Relación con el acta (Documento)
            $table->foreignId('document_id')
                ->constrained('documents')
                ->onDelete('cascade');
            // Relación con el usuario que debe firmar
            $table->foreignId('user_id')
                ->constrained('users');
            // Orden de la firma (1, 2, 3...)
            $table->integer('order')->default(1);
            // Estado de la firma
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acta_signatures');
    }
};
