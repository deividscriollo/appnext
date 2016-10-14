<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistMensajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mensajeriaconex')->create('blacklist_mensajes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_mensaje');
            $table->string('id_empresa');
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
        Schema::connection('mensajeriaconex')->drop('blacklist_mensajes');
    }
}
