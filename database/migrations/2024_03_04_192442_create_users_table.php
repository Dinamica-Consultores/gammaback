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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();;
            $table->string('password');
            $table->integer('level_user');
            $table->string('mes')->nullable();
            $table->string('ano')->nullable();
            $table->string('max_var')->nullable();
            $table->string('code_forgetpassword')->nullable();
            $table->string('min_var')->nullable();
            $table->integer('id_company_show')->nullable();
            $table->integer('id_group_show')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
};
