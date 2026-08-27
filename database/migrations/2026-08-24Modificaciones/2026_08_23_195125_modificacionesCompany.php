<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('cuenta_anticipos_ipat')->nullable();
            $table->string('cuenta_anticipos_irae')->nullable();
            $table->decimal('porcentaje_irae', 5, 2)->default(25.00);
            $table->decimal('porcentaje_ipat', 5, 2)->default(1.50);
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['cuenta_anticipos_ipat','cuenta_anticipos_irae', 'porcentaje_irae', 'porcentaje_ipat']);
        });
    }
};