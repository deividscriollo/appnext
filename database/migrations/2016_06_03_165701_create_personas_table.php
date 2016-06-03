<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('personas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('cedula');
            $table->string('Nombres_apellidos');
            $table->string('provincia');
            $table->string('canton');
            $table->string('parroquia');
            $table->string('zona');
            $table->string('correo');
            $table->string('telefono');
            $table->string('celular');
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
        Schema::drop('personas');
    }
}
