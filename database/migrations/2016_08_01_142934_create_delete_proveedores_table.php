<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeleteProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('facturanexconex')->create('blacklist_proveedores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_sucursal');
            $table->string('id_proveedor');
            $table->foreign('id_sucursal')->references('id_sucursal')->on('registro.sucursales')->onDelete('cascade');
            $table->foreign('id_proveedor')->references('id')->on('registro.proveedores')->onDelete('cascade');
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
        Schema::connection('facturanexconex')->drop('blacklist_proveedores');
    }
}
