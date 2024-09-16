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
        Schema::table('round_types', function (Blueprint $table) {});

        DB::table('round_types')
            ->where('id',  5)
            ->delete();
        DB::table('round_types')
            ->where('id',  6)
            ->delete();

        DB::table('round_types')
            ->where('id',  7)
            ->delete();
        DB::table('round_types')
            ->where('id',  8)
            ->delete();

        DB::table('round_types')
            ->where('id',  12)
            ->delete();
        DB::table('round_types')
            ->where('id',  13)
            ->delete();

        DB::table('round_types')
            ->where('id',  9)
            ->delete();
        DB::table('round_types')
            ->where('id',  10)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('round_types', function (Blueprint $table) {
            //
        });
    }
};
