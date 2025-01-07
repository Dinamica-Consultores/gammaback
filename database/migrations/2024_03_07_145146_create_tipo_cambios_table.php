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
        Schema::create('tipo_cambios', function (Blueprint $table) {
            $table->id('id');
            $table->string('mes')->nullable();
            $table->string('ano')->nullable();
            $table->datetime('fecha');
            $table->string('dolar_compra');
            $table->string('dolar_venta');
            $table->string('dolar_promedio');
            $table->string('euro_promedio');
            $table->string('francosuizo_promedio');
            $table->string('ui');
            $table->string('ipc');
            $table->string('ipc_empresa');
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
        Schema::drop('tipo_cambios');
    }
};
