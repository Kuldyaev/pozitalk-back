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
        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 5,
            'number' => 2,
        ]);
        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 6,
            'number' => 3,
        ]);

        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 7,
            'number' => 2,
        ]);
        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 8,
            'number' => 3,
        ]);

        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 9,
            'number' => 1,
        ]);
        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 10,
            'number' => 2,
        ]);
        DB::table('user_accounts')->insert([
            'user_id' => 1,
            'active' => false,
            'role_id' => 3,
            'circle' => 1,
            'available' => 1,
            'next_round' => 11,
            'number' => 3,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
