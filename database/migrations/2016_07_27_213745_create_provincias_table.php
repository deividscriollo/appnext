<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvinciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('localizacionconex')->create('provincias', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre')->nullable();
            $table->string('codtelefonico')->nullable();
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
        Schema::connection('localizacionconex')->drop('provincias');
    }
}
