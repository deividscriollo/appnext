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
         Schema::connection('pgsql')->create('colaboradores', function (Blueprint $table) {
            $table->string('id_colaborador')->primary();
            $table->string('correo');
            $table->string('pass');
            $table->integer('estado');
            $table->string('id_empresa');
            $table->timestamps();
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql')->drop('colaboradores');
    }
}
