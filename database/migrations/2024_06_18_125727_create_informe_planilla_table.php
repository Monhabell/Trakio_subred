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
        Schema::create('informe_planilla', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // id del usuario que cargo el document
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->string('mesCertificacion');
            $table->string('año');
            $table->string('numPlanilla')->unique();
            $table->date('fechaPago');
            $table->decimal('valorEps', 15, 2);
            $table->decimal('valorAfp', 15, 2);
            $table->decimal('arlValor', 15, 2);
            $table->decimal('valorCompensacion', 15, 2)->nullable();
            $table->decimal('totalPlanilla', 15, 2);
            $table->string('cargueDoc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informe_planilla');
    }
};