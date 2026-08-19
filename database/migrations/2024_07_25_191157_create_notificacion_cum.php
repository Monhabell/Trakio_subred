<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificacion_cumpleaños', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->foreign('notifiable_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_bithday');
            $table->foreign('id_bithday')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('is_user_likes');
            $table->foreign('is_user_likes')->references('id')->on('users')->onDelete('cascade');
            $table->json('data');
            $table->integer('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificacion_cumpleaños');
    }
};
