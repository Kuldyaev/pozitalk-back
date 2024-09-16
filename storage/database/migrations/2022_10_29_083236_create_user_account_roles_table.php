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
        Schema::create('user_account_roles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
        });

        DB::table('user_account_roles')->insert([
            'title' => 'Вне очереди',
        ]);
        DB::table('user_account_roles')->insert([
            'title' => 'Даритель',
        ]);
        DB::table('user_account_roles')->insert([
            'title' => 'Получатель',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_account_roles');
    }
};
