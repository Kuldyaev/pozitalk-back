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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });

        DB::table('user_roles')->insert([
            'title' => 'user'
        ]);
        DB::table('user_roles')->insert([
            'title' => 'moderator'
        ]);
        DB::table('user_roles')->insert([
            'title' => 'admin'
        ]);
        DB::table('user_roles')->insert([
            'title' => 'super-admin'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
};
