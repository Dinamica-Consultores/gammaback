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
        Schema::create('documento_companias', function (Blueprint $table) {
            $table->id('id');
            $table->string('nombre');
            $table->datetime('fecha_de_vencimiento');
            $table->datetime('fecha_de_generacion');
            $table->integer('id_tipodocumento');
            $table->integer('id_compania');
            $table->text('url');
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
        Schema::drop('documento_companias');
    }
};
