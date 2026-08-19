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
        // compo nuevo booll despues de has_hc
        Schema::table('licenses', function (Blueprint $table) {
            $table->boolean('has_validator_bd')->default(false)->after('has_hc');
            $table->boolean('has_comprobador')->default(false)->after('has_validator_bd');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licences', function (Blueprint $table) {
            $table->dropColumn('has_validator_bd');
            $table->dropColumn('has_comprobador');
        });
    }
};
