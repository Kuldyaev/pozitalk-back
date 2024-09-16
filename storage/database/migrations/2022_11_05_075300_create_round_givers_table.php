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
        Schema::create('round_givers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('round_id')->unsigned()->nullable();
            $table->foreign('round_id')->references('id')->on('rounds');

            $table->bigInteger('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('round_giver_statuses');

            $table->bigInteger('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('user_accounts');

            $table->string('start')->nullable();
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
        Schema::dropIfExists('round_givers');
    }
};
