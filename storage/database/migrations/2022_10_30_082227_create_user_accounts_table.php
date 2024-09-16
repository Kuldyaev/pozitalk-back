<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('active');
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('user_account_roles');
            $table->integer('next_round');
            $table->integer('circle');
            $table->integer('number');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });

        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => true,
            'role_id' => 3,
            'next_round' => 1,
            'circle' => 1,
            'number' => 1,
        ]);

        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'next_round' => 2,
            'circle' => 1,
            'number' => 2,
        ]);

        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'next_round' => 3,
            'circle' => 1,
            'number' => 3,
        ]);

        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'next_round' => 4,
            'circle' => 1,
            'number' => 4,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_accounts');
    }
};
