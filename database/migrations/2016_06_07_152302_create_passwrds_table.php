<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswrdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('ingresoconex')->create('passwrdsE', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('pass_email');
            $table->string('password');
            $table->string('remember_token',500);
            $table->integer('pass_estado');
            $table->string('id_user');
            $table->foreign('id_user')->references('id_empresa')->on('registro.empresas')->onDelete('cascade');
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
        Schema::connection('ingresoconex')->drop('passwrdsE');
    }
}
