<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUbicacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('inventarioconex')->create('ubicacions', function (Blueprint $table) {
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
        Schema::connection('inventarioconex')->drop('ubicacions');
    }
}
