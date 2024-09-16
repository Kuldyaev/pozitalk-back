<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('index_auto_purchases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->integer('program_id');
            $table->integer('error_code');
            $table->double('amount', 8, 2);
            $table->integer('wallet_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('regularity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('index_auto_purchases');
    }
};
