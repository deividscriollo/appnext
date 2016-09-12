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
            $table->string('num_factura')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('Ruc_prov')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->string('clave_acceso')->nullable();
            $table->string('ambiente')->nullable();
            $table->string('tipo_doc')->nullable();
            $table->string('tipo_consumo')->nullable();
            $table->string('total')->nullable();
            $table->string('subtotal_12',50)->nullable();
            $table->string('subtotal_0',50)->nullable();
            $table->string('subtotal_no_sujeto',50)->nullable();
            $table->string('subtotal_exento_iva',50)->nullable();
            $table->string('subtotal_sin_impuestos',50)->nullable();
            $table->string('descuento',50)->nullable();
            $table->string('ice',50)->nullable();
            $table->string('iva_12',50)->nullable();
            $table->string('propina',50)->nullable();
            $table->string('contenido_fac',20000)->nullable();
            $table->string('id_empresa')->nullable();
            $table->boolean('estado')->nullable();
            $table->boolean('estado_view')->nullable();
            $table->foreign('id_empresa')->references('id_empresa')->on('registro.empresas')->onDelete('cascade');
            $table->string('id_sucursal');
           // $table->foreign('id_sucursal')->references('id_empresa')->on('registro.empresas')->onDelete('cascade');
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
