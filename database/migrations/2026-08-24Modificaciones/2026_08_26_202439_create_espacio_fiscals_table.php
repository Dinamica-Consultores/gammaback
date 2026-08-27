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
        Schema::create('espacio_fiscals', function (Blueprint $table) {
            $table->id('id');
            $table->integer('ano');
            $table->integer('mes');
            $table->string('nivel_3');
            $table->string('ajusta');
            $table->string('tipo');
            $table->string('operador');
            $table->string('signo');
            $table->string('monto_valor');
            $table->string('sucursal');
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
        Schema::drop('espacio_fiscals');
    }
};
