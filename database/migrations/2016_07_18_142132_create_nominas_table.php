<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mod_radioconex')->create('nominas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('descripcion');
            $table->string('periodicidad');
            $table->string('registro_patronal');
            $table->string('dias');
            $table->date('fecha_inicio');
            $table->string('sucursal');
            // $table->foreign('id_sucursal')->references('registro.sucursales')->onDelete('cascade');
            $table->integer('estado');
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
        Schema::connection('mod_radioconex')->drop('nominas');
    }
}
