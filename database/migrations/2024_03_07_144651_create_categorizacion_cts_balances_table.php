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
        Schema::create('categorizacion_cts_balances', function (Blueprint $table) {
            $table->id('id');
            $table->string('cuenta');
            $table->string('nombre');
            $table->string('origen');
            $table->string('nivel_1');
            $table->string('nivel_2');
            $table->string('nivel_3');
            $table->string('nivel_4');
            $table->string('posicion_moneda');
            $table->string('posicion_fiscal');
            $table->string('posicion_socios');
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
        Schema::drop('categorizacion_cts_balances');
    }
};
