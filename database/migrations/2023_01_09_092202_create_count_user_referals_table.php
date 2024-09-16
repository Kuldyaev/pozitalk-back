<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('count_user_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('line_one')->default(0);
            $table->integer('line_two')->default(0);
            $table->integer('line_three')->default(0);
            $table->integer('line_four')->default(0);
            $table->integer('line_five')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('count_user_referrals');
    }
};
