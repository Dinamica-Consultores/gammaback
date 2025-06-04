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
        Schema::table('tipo_cambios', function (Blueprint $table) {
         
            $table->dropColumn('dolar_compra');
            $table->dropColumn('dolar_venta');
            $table->dropColumn('dolar_promedio');
            $table->dropColumn('euro_promedio');
            $table->dropColumn('francosuizo_promedio');
            $table->dropColumn('ui');
            $table->dropColumn('ipc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_cambios', function (Blueprint $table) {
            //
        });
    }
};
