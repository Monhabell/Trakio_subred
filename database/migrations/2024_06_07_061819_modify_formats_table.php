<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('formats', function (Blueprint $table) {
            $table->decimal('header_time', 8, 2)->change();
            $table->decimal('body_time', 8, 2)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('formats', function (Blueprint $table) {
            $table->integer('header_time')->change();
            $table->integer('body_time')->change();
        });
    }
};
