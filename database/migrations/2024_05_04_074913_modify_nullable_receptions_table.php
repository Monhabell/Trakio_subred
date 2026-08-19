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
        Schema::table('receptions', function (Blueprint $table) {
             $table->unsignedBigInteger('package_id')->nullable()->change();
             $table->unsignedBigInteger('typed_by')->nullable()->change();             
             $table->dateTime('typed_date')->nullable()->change();
             $table->integer('type')->nullable()->change();
             $table->dateTime('return_date')->nullable()->change();
             $table->unsignedBigInteger('returned_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptions', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
            $table->unsignedBigInteger('typed_by')->nullable(false)->change();            
            $table->dateTime('typed_date')->nullable(false)->change();
            $table->integer('type')->nullable(false)->change();
            $table->dateTime('return_date')->nullable(false)->change();
            $table->unsignedBigInteger('returned_by')->nullable(false)->change();
        });
    }
};