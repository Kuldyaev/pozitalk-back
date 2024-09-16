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
        Schema::create('round_types', function (Blueprint $table) {
            $table->id();
            $table->integer('price');
            $table->integer('count_rounds');
            $table->integer('count_givers');
        });

        DB::table('round_types')->insert([
            'price' => 50,
            'count_rounds' => 3,
            'count_givers' => 3,
        ]);
        DB::table('round_types')->insert([
            'price' => 100,
            'count_rounds' => 3,
            'count_givers' => 3,
        ]);
        DB::table('round_types')->insert([
            'price' => 200,
            'count_rounds' => 3,
            'count_givers' => 3,
        ]);
        DB::table('round_types')->insert([
            'price' => 400,
            'count_rounds' => 3,
            'count_givers' => 3,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('round_types');
    }
};
