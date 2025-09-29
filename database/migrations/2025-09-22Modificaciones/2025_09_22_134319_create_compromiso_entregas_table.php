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
        Schema::create('compromiso_entregas', function (Blueprint $table) {
            $table->id('id');
            $table->timestamp('fecha_entrega')->nullable();
            $table->timestamp('fecha_entregado')->nullable();
            $table->timestamp('fecha_reunion')->nullable();
            $table->integer('usuario');
            $table->integer('usuario_entregado')->nullable();
            $table->integer('id_company');
            $table->string('descripcion_entregado')->nullable();;
            $table->string('descripcion_entrega')->nullable();;
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
        Schema::drop('compromiso_entregas');
    }
};
