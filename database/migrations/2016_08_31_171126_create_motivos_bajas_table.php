<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotivosBajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventarioconex')->create('motivos_bajas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('codigo')->nullable();
            $table->string('descripcion')->nullable();
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
        Schema::connection('inventarioconex')->drop('motivos_bajas');
    }
}
