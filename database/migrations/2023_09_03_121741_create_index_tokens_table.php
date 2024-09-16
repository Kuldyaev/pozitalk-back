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
        Schema::create('index_tokens', function (Blueprint $table) {
            $table->id();
            $table->double('index');
            $table->double('bitcoin');
            $table->double('ethereum');
            $table->double('arbitrum');
            $table->double('optimism');
            $table->double('polygon');
            $table->double('polkadot');
            $table->double('ton');
            $table->double('aave');
            $table->double('apecoin');
            $table->boolean('is_rebalancing')->nullable();
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
        Schema::dropIfExists('index_tokens');
    }
};
