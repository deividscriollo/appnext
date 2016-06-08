<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswrdsPsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('ingresoconex')->create('passwrdsP', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pass_email');
            $table->string('pass_nextbook');
            $table->string('id_user');
            // $table->foreign('id_user')->references('id_persona')->on('personas')->onDelete('cascade');
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
       Schema::connection('ingresoconex')->drop('passwrdsP');
    }
}
