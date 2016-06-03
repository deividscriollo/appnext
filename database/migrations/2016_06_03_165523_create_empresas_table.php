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
        Schema::create('empresas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('razon_social');
            $table->string('nombre_comercial');
            $table->string('estado_contribuyente');
            $table->string('tipo_contribuyente');
            $table->string('obligado_contabilidad');
            $table->string('actividad_economica');
            $table->string('nombres_apellidos');
            $table->string('fecha_nacimiento');
            $table->string('correo');
            $table->string('telefono');
            $table->string('celular');
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
        Schema::drop('empresas');
    }
}
