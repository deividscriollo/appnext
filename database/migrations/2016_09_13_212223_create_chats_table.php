<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mensajeriaconex')->create('chats', function (Blueprint $table) {
            $table->string('chat_id')->primary();
            $table->string('user1_id')->nullable();
            $table->string('user2_id')->nullable();
            $table->boolean('estado')->nullable();
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
        Schema::connection('mensajeriaconex')->drop('chats');
    }
}
