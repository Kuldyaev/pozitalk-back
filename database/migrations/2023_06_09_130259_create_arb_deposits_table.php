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
        Schema::create('arb_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('amount');
            $table->integer('count_months');
            $table->double('percent');
            $table->date('start')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_can_request')->default(false);
            $table->boolean('is_request')->default(false);
            $table->boolean('is_wont_request')->default(false)->nullable();
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
        Schema::dropIfExists('arb_deposits');
    }
};
