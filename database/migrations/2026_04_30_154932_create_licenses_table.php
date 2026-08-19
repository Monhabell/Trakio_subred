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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('program_name'); // 'ODIN' o 'PAIWEB'
            $table->string('license_key')->unique();
            $table->string('client_name')->nullable();
            $table->enum('type', ['trial', 'forever'])->default('trial');
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('hwid')->nullable(); // Opcional: para amarrar la licencia a un solo PC
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
