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
        Schema::connection('pgsql')->create('clientes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('ruc_empresa')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('actividad_economica')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('representante_legal')->nullable();
            $table->string('cedula_representante')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('correo')->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('google')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('imagen')->nullable();
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
