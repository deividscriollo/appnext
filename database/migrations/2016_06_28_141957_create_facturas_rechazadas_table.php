<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturasRechazadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('facturanexconex')->create('facturas_rechazadas', function (Blueprint $table) {
            $table->string('id_factura_r')->primary();
            // $table->string('num_factura');
            // $table->string('nombre_comercial');
            // $table->string('Ruc_prov');
            // $table->string('fecha_emision');
            // $table->string('clave_acceso')->unique();
            // $table->string('ambiente');
            // $table->string('tipo_doc');
            // $table->string('total');
            $table->string('nombre_doc');
            $table->string('razon_rechazo');
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
        Schema::connection('facturanexconex')->drop('facturas_rechazadas');
    }
}
