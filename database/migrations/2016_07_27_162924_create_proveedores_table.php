<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('proveedores', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('razon_social');
            $table->string('nombre_comercial');
            $table->string('ruc');
            $table->string('dir_matriz');
            $table->string('dir_establecimiento');
            $table->string('id_empresa');
            $table->integer('estado');
            $table->foreign('id_empresa')->references('id_empresa')->on('registro.empresas')->onDelete('cascade');
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
        Schema::connection('pgsql')->drop('proveedores');
    }
}
