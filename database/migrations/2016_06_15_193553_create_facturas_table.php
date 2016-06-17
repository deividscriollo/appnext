<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('facturanexconex')->create('facturas', function (Blueprint $table) {
            $table->string('id_factura')->primary();
            $table->string('num_fac');
            $table->string('date_fe');
            $table->string('totalSinImpuestos');
            $table->string('totalDescuento');
            // $table->string('propina');
            $table->string('importeTotal');
            $table->string('codDoc');
            $table->string('contenido_fac',10000);
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
        Schema::connection('facturanexconex')->drop('facturas');
    }
}
