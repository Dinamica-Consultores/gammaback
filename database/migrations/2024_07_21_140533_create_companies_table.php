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
        Schema::create('companies', function (Blueprint $table) {
            $table->id('id');
            $table->string('razon_social');
            $table->string('per_cont_name');
            $table->string('per_cont_email');
            $table->string('per_cont_phone');
            $table->string('logo')->nullable();
            $table->string('campo')->nullable();
            $table->boolean('ispresupuesto');
            $table->boolean('isestadosp');
            $table->integer('id_estudio');
            $table->string('mes')->nullable();
            $table->string('ano')->nullable();
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
        Schema::drop('companies');
    }
};
