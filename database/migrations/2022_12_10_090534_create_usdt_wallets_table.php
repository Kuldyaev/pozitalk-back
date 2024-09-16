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
        Schema::create('usdt_wallets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->integer('active');
            $table->string('wallet');
            $table->string('private_key');
            $table->string('public_key');
            $table->string('product')->nullable();
            $table->string('contract_address');
            $table->dateTime('date');
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
        Schema::dropIfExists('usdt_wallets');
    }
};
