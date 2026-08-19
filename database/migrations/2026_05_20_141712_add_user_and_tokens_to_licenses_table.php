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
        Schema::table('licenses', function (Blueprint $table) {
            // 1. Relación con el usuario (puede ser NULL si generas llaves masivas antes de venderlas)
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');

            // 2. Características del Plan adquirido
            $table->string('plan_name')->default('free')->after('program_name'); // 'free', 'starter', 'pro', 'business'
            $table->integer('tokens_available')->default(100)->after('plan_name');

            // 3. Permisos de módulos específicos para Python (Booleanos / tinyint)
            $table->boolean('has_hc')->default(false)->after('is_active');
            $table->boolean('has_gesiform')->default(false)->after('has_hc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'plan_name', 'tokens_available', 'has_hc', 'has_gesiform']);
        });
    }
};
