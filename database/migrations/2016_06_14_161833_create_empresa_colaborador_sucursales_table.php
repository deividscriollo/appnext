<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresaColaboradorSucursalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('personalizacionconex')->create('empresa_colaborador_sucursales', function (Blueprint $table) {
            $table->increments('id');
            // $table->string('id_empresa');
            $table->string('id_colaborador');
            $table->integer('id_sucursal');
            // $table->foreign('id_empresa')->references('id_empresa')->on('registro.empresas')->onDelete('cascade');
            $table->foreign('id_colaborador')->references('id_colaborador')->on('registro.colaboradores')->onDelete('cascade');
            $table->foreign('id_sucursal')->references('id_sucursal')->on('registro.sucursales')->onDelete('cascade');
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
        Schema::connection('personalizacionconex')->drop('empresa_colaborador_sucursales');
    }
}
