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
        Schema::create('token_rates', function (Blueprint $table) {
            $table->id();
            $table->double('private_rate');
            $table->double('classic_rate');
            $table->timestamps();
        });

        DB::table('token_rates')->insert([
            'private_rate' => 0.01,
            'classic_rate' => 0.02,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('token_rates');
    }
};
