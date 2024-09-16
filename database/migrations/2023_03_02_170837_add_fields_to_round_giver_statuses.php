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
        Schema::table('round_giver_statuses', function (Blueprint $table) {
        });
        DB::table('round_giver_statuses')->insert([
            'id' => 8,
            'title' => 'Ждет оплаты'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('round_giver_statuses', function (Blueprint $table) {
            //
        });
    }
};
