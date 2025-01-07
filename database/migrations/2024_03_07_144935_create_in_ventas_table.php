<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_ventas', function (Blueprint $table) {
            $table->id('id');
            $table->string('mes');
            $table->string('ano');
            $table->string('sucursal');
            $table->string('codigo_analisis');
            $table->string('cantidad_venta_unidades');
            $table->string('cantidad_costo');
            $table->string('cantidad_margen');
            $table->string('ventas_uyu');
            $table->string('ganancia_bruta_uyu');
            $table->string('ventas_uyu_prom');
            $table->string('ganancia_bruta_uyu_prom');
            $table->string('costo_uyu');
            $table->integer('id_excel');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('in_ventas');
    }
};
