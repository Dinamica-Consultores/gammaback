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
        Schema::create('clasificacion_cuenta_resuls', function (Blueprint $table) {
            $table->id('id');
            $table->string('cuenta');
            $table->string('nombre');
            $table->string('grupo');
            $table->string('origen');
            $table->string('nivel_1');
            $table->string('nivel_2');
            $table->string('nivel_3');
            $table->string('clasificacion_ratios_financ');
            $table->string('clasificacion_punto_equilibrio');
            $table->string('clasificacion_cuenta_juridica_legal');
            $table->string('clasificacion_ebit_ebitda');
            $table->string('clasificacion_er');
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
        Schema::drop('clasificacion_cuenta_resuls');
    }
};
