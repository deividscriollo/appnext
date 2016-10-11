<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogoContraportadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('catalogoconex')->create('catalogo_contraportadas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('img')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('estado')->nullable();
            $table->string('id_sucursal')->nullable();
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
        Schema::connection('catalogoconex')->drop('catalogo_contraportadas');
    }
}
