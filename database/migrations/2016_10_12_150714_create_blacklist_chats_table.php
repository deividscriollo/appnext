<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlacklistChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mensajeriaconex')->create('blacklist_chats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_chat');
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
        Schema::connection('mensajeriaconex')->drop('blacklist_chats');
    }
}
