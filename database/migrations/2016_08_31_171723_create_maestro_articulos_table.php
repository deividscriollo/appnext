<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaestroArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventarioconex')->create('maestro_articulos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('codigo')->nullable();
            $table->string('grupo')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('color')->nullable();
            $table->string('anio_fabricacion')->nullable();
            $table->string('num_partes')->nullable();
            $table->string('num_placa')->nullable();
            $table->string('num_serie')->nullable();
            $table->string('pais_origen')->nullable();
            $table->string('num_factura')->nullable();
            $table->string('num_guia')->nullable();
            $table->string('fecha_adquisicion')->nullable();
            $table->string('valor_compra')->nullable();
            $table->string('modo_adquisicion')->nullable();
            $table->string('estado_bn')->nullable();
            $table->string('vida_util')->nullable();
            $table->string('observaciones')->nullable();
            $table->string('fecha_inicio')->nullable();
            $table->integer('estado')->nullable();
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
        Schema::connection('inventarioconex')->drop('maestro_articulos');
    }
}
