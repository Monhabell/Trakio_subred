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
        Schema::table('data_users', function (Blueprint $table) {        
            $table->string('address')->nullable()->change();
            $table->string('rh', 3)->nullable()->change();
            $table->string('ethnicity')->nullable()->change();
            $table->string('url_img')->nullable()->change();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_users', function (Blueprint $table) {        
            $table->string('address')->nullable(false)->change();
            $table->string('rh', 3)->nullable(false)->change();
            $table->string('ethnicity')->nullable(false)->change();
            $table->string('url_img')->nullable(false)->change();
        });
    }
};
