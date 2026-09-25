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
        Schema::dropIfExists('espacio_fiscals');
        // 1. Modificar tabla clasificacion_cuenta_resuls
        Schema::table('clasificacion_cuenta_resuls', function (Blueprint $table) {
            $table->string('espacio_fiscal_ajuste')->nullable();
            $table->string('irae')->nullable();
        });

        // 2. Modificar tabla categorizacion_cts_balances
        Schema::table('categorizacion_cts_balances', function (Blueprint $table) {
            $table->string('espacio_fiscal_ajuste')->nullable();
        });

        // 3. Modificar tablas in_balances, in_presupuestos e in_resultados
        $tablasIn = ['in_balances', 'in_presupuestos', 'in_resultados'];

        foreach ($tablasIn as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->string('operador')->nullable();
                $table->string('signo', 2)->nullable();
                $table->decimal('monto_valor', 15, 2)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir cambios en clasificacion_cuenta_resuls
        Schema::table('clasificacion_cuenta_resuls', function (Blueprint $table) {
            $table->dropColumn(['espacio_fiscal_ajuste', 'irae']);
        });

        // Revertir cambios en categorizacion_cts_balances
        Schema::table('categorizacion_cts_balances', function (Blueprint $table) {
            $table->dropColumn('espacio_fiscal_ajuste');
        });

        // Revertir cambios en in_balances, in_presupuestos e in_resultados
        $tablasIn = ['in_balances', 'in_presupuestos', 'in_resultados'];

        foreach ($tablasIn as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn(['operador', 'signo', 'monto_valor']);
            });
        }
    }
};