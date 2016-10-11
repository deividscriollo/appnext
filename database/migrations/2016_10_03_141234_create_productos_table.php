<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalogoconex')->create('productos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('codigo');
            $table->string('descripcion');
            $table->string('nombre');
            $table->string('stock');
            $table->string('precio_unitario');
            $table->string('precio_oferta');
            $table->string('id_sucursal');
            $table->string('estado');
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
        Schema::connection('catalogoconex')->drop('productos');
    }
}
