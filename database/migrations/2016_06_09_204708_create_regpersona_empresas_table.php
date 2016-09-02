<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegpersonaEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('regpersona_empresas', function (Blueprint $table) {
            $table->string('idp_regE')->primary();
            $table->string('cedula')->nullable();
            $table->string('nombres_apellidos')->nullable();
            $table->string('fecha_nacimiento')->nullable();
            $table->string('correo')->nullable();
            $table->string('provincia')->nullable();
            $table->string('canton')->nullable();
            $table->string('telefono')->nullable();
            // $table->string('celular')->nullable();
            $table->integer('estado')->nullable();
            $table->string('id_empresa');
            $table->foreign('id_empresa')->references('id_empresa')->on('empresas')->onDelete('cascade');
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
        Schema::connection('pgsql')->drop('regpersona_empresas');
    }
}
