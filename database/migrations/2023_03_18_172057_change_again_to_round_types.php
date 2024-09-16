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
        Schema::table('round_types', function (Blueprint $table) {
            //
        });

        DB::table('round_types')
            ->where('id',  1)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 1,
                'is_need_pay' => false
            ]);

        DB::table('round_types')
            ->where('id',  2)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 2
            ]);
        DB::table('round_types')
            ->insert([
                'price' => 150,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 2,
                'is_need_pay' => true
            ]);
        DB::table('round_types')
            ->insert([
                'price' => 250,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 2,
                'is_need_pay' => true
            ]);

        DB::table('round_types')
            ->where('id',  3)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 3,
            ]);
        DB::table('round_types')
            ->where('id',  5)
            ->update([
                'price' => 300,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 3,
                'is_need_pay' => true
            ]);
        DB::table('round_types')
            ->where('id',  6)
            ->update([
                'price' => 500,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 3,
                'is_need_pay' => true
            ]);

        DB::table('round_types')
            ->where('id',  4)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 4
            ]);
        DB::table('round_types')
            ->where('id',  7)
            ->update([
                'price' => 600,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 4,
                'is_need_pay' => true
            ]);
        DB::table('round_types')
            ->where('id',  8)
            ->update([
                'price' => 800,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 4,
                'is_need_pay' => true
            ]);
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
