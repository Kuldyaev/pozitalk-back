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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('user_accounts');

            $table->bigInteger('round_type_id')->unsigned();
            $table->foreign('round_type_id')->references('id')->on('round_types');

            $table->boolean('active');
            $table->integer('verification_code');
            $table->integer('price');
            $table->timestamps();
        });

        DB::table('rounds')->insert([
            'account_id' => 1,
            'round_type_id' => 1,
            'active' => true,
            'verification_code' => rand(1000, 9999),
            'price' => 50,
        ]);

        DB::table('rounds')->insert([
            'account_id' => 2,
            'round_type_id' => 2,
            'active' => true,
            'verification_code' => rand(1000, 9999),
            'price' => 100,
        ]);

        DB::table('rounds')->insert([
            'account_id' => 3,
            'round_type_id' => 3,
            'active' => true,
            'verification_code' => rand(1000, 9999),
            'price' => 200,
        ]);

        DB::table('rounds')->insert([
            'account_id' => 4,
            'round_type_id' => 4,
            'active' => true,
            'verification_code' => rand(1000, 9999),
            'price' => 400,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rounds');
    }
};
