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
        Schema::create('bitacoras_envios_documentos', function (Blueprint $table) {
            $table->id('id');
            $table->integer('id_documento_companias');
            $table->date('fecha_envio');
            $table->text('correos');
            $table->text('texto_data');
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
        Schema::drop('bitacoras_envios_documentos');
    }
};
