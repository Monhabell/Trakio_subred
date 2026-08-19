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
        Schema::create('productivity_calculation', function (Blueprint $table) {
            $table->id();
            $table->integer('sds_id');
            $table->integer('section_id');
            $table->string('file_number');
            $table->float('calculation_time');
            $table->float('inactivity_time');
            $table->string('user_sds');
            $table->integer('pages')->nullable();
            $table->integer('active_page')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productivity_calculation');
    }
};
