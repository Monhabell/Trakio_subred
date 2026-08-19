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
            #quitar relacion y campo 
            $table->dropForeign('receptions_typed_by_foreign');
            $table->dropColumn('typed_by');
            $table->dropColumn('typed_date');
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('typed_by');
            $table->foreign('typed_by')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('typed_date');
            $table->integer('type');
        });
    }
};
