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
        Schema::create('estudios', function (Blueprint $table) {
            $table->id('id');
            $table->string('razon_social');
            $table->integer('cantida_empresa_max')->nullable();
            $table->boolean('es_empresa');
            $table->string('per_cont_name');
            $table->string('per_cont_phone');
            $table->string('logo');
            $table->string('campo');
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
        Schema::drop('estudios');
    }
};
