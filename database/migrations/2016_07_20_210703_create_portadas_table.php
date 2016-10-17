<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('personalizacionconex')->create('portadas', function (Blueprint $table) {
            $table->string('id_img_portada')->primary();
            $table->string('img');
            $table->integer('estado');
            $table->boolean('estado_delete');
            $table->string('id_empresa');
            $table->foreign('id_empresa')->references('id_empresa')->on('registro.empresas')->onDelete('cascade');
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
        Schema::connection('personalizacionconex')->drop('portadas');
    }
}
