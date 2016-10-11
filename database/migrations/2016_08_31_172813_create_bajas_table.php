<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventarioconex')->create('bajas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('maestro_articulo')->nullable();
            $table->string('motivos_baja')->nullable();
            $table->string('fecha_baja')->nullable();
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
        Schema::connection('inventarioconex')->drop('bajas');
    }
}
