<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('empresas', function (Blueprint $table) {
            $table->string('id_empresa')->primary();
            $table->string('Ruc');
            $table->string('razon_social');
            $table->string('nombre_comercial');
            $table->string('estado_contribuyente');
            $table->string('tipo_contribuyente');
            $table->string('obligado_contabilidad');
            $table->string('actividad_economica');
            $table->string('codigo_activacion');
            $table->integer('estado');
            $table->integer('id_provincia');
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
        Schema::connection('pgsql')->drop('empresas');
    }
}
