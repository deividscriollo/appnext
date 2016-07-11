<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = $request->file('file');
        Schema::connection('pgsql')->create('clientes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre_comercial');
            $table->string('actividad_economica');
            $table->string('razon_social');
            $table->string('representante_legal');
            $table->string('cedula_representante');
            $table->string('celular');
            $table->string('telefono');
            $table->string('direccion');
            $table->string('correo');
            $table->string('sitio_web');
            $table->string('facebook');
            $table->string('twitter');
            $table->string('google');
            $table->string('observaciones');
            $table->string('imagen');
            $table->string('estado');
            // $table->string('fecha_creacion');
            $table->string('id_empresa');
            // $table->foreign('id_empresa')->references('registro.clientes')->onDelete('cascade');
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
        Schema::connection('pgsql')->drop('clientes');
    }
}
