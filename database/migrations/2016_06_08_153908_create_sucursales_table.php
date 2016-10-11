<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSucursalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('sucursales', function (Blueprint $table) {
            $table->increments('id_sucursal');
            $table->string('nombre_sucursal');
            $table->string('codigo');
            $table->string('direccion');
            $table->string('estado');
            $table->string('descripcion',10000)->nullable();;
            $table->string('id_empresa');
            $table->string('categoria',50)->nullable();;
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
        Schema::connection('pgsql')->drop('sucursales');
    }
}
