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
        Schema::create('in_presupuestos', function (Blueprint $table) {
            $table->id('id');
            $table->string('mes');
            $table->string('ano');
            $table->string('sucursal');
            $table->string('cuenta_master');
            $table->string('monto_uyu');
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
        Schema::drop('in_presupuestos');
    }
};
