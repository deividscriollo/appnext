<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImgPerfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('personalizacionconex')->create('img_perfiles', function (Blueprint $table) {
            $table->string('id_img_perfil')->primary();
            $table->string('img');
            $table->integer('estado');
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
        Schema::connection('personalizacionconex')->drop('img_perfiles');
    }
}
