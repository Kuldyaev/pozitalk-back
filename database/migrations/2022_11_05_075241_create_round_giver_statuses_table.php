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
        Schema::create('round_giver_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });

        DB::table('round_giver_statuses')->insert([
            'title' => 'Получателя нет'
        ]);
        DB::table('round_giver_statuses')->insert([
            'title' => 'В процессе'
        ]);
        DB::table('round_giver_statuses')->insert([
            'title' => 'Отправил'
        ]);
        DB::table('round_giver_statuses')->insert([
            'title' => 'Подтверждено'
        ]);
        DB::table('round_giver_statuses')->insert([
            'title' => 'Отмменен'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('round_giver_statuses');
    }
};
