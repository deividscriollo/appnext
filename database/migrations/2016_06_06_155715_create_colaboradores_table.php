<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColaboradoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('colaboradores', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('correo');
            $table->string('pass');
            $table->string('estado');
            $table->string('id_empresa');
            $table->timestamps();
            $table->foreign('id_empresa')->references('id')->on('empresas')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('colaboradores');
    }
}
