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
            $table->integer('queue')->nullable();
            $table->boolean("is_need_pay")->default(false);
        });

        DB::table('round_types')
            ->where('id',  1)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 1
            ]);

        DB::table('round_types')
            ->where('id',  2)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 2,
                'queue' => 1
            ]);

        DB::table('round_types')
            ->where('id',  3)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 2,
            ]);
        DB::table('round_types')
            ->insert([
                'price' => 300,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 2,
                'is_need_pay' => true
        ]);
        DB::table('round_types')
            ->insert([
                'price' => 500,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 2,
                'is_need_pay' => true
            ]);

        DB::table('round_types')
            ->where('id',  4)
            ->update([
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 3
            ]);
        DB::table('round_types')
            ->insert([
                'price' => 600,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 3,
                'is_need_pay' => true
            ]);
        DB::table('round_types')
            ->insert([
                'price' => 900,
                'count_rounds' => 1,
                'count_givers' => 3,
                'queue' => 3,
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
            $table->dropColumn("queue");
            $table->dropColumn("is_need_pay");
        });
    }
};
