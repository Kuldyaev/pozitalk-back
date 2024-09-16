<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('user_roles');
            $table->bigInteger('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('user_statuses');
            $table->string('telegram_name')->nullable();
            $table->string('telegram_id')->nullable();
            $table->bigInteger('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('code')->nullable();
            $table->string('code_generated_at')->nullable();
            $table->string('referal_id')->nullable();
            $table->string('referal_invited')->unique();
            $table->string('message')->default('');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert([
            'login' => 'Богдан',
            'phone' => 34642053467,
            'role_id' => 4,
            'status_id' => 1,
            'telegram_id' => 305942945,
            'phone_verified_at' => Carbon::now(),
            'telegram_name' => 'vitalibrinkmann',
            'code' => mb_strtoupper(\Str::random(8)),
            'code_generated_at' => Carbon::now(),
            'referal_invited' => base64_encode('89092929292'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
