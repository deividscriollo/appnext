<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMensajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mensajeriaconex')->create('chat_mensajes', function (Blueprint $table) {
            $table->string('chat_mensajes_id')->primary();
            $table->string('chat_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('mensaje')->nullable();
            $table->boolean('estado_view')->nullable();
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
        Schema::connection('mensajeriaconex')->drop('chat_mensajes');
    }
}
