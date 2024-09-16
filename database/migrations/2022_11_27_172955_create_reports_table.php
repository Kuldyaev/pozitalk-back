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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('to_id')->unsigned();
            $table->foreign('to_id')->references('id')->on('user_accounts');

            $table->bigInteger('from_id')->unsigned();
            $table->foreign('from_id')->references('id')->on('user_accounts');

            $table->bigInteger('round_id')->unsigned();
            $table->foreign('round_id')->references('id')->on('rounds');

            $table->bigInteger('amount');

            $table->boolean('successful');
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
        Schema::dropIfExists('reports');
    }
};
