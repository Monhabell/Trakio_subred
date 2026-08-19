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
        Schema::table('consecutivos', function (Blueprint $table) {
            // Asegúrate de que 'id_ficha' exista en tu tabla consecutivos.
            // Si 'id_ficha' no es una clave primaria de entregas, puedes usar unsignedBigInteger().
            $table->unsignedBigInteger('id_ficha')->change();

            // Agregamos la clave foránea
            $table->foreign('id_ficha')->references('id')->on('receptions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('consecutivos', function (Blueprint $table) {
            // Eliminamos la clave foránea en el método down
            $table->dropForeign(['id_ficha']);
        });
    }

};