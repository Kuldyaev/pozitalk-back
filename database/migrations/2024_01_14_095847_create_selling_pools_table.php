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
        Schema::create('selling_pools', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('selling_id')->unsigned();
            $table->string('key');
            $table->double('sum');
            $table->integer('participants');
            $table->double('sum_per_participant');
            $table->timestamps();

            $table->foreign('selling_id')->references('id')->on('sellings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selling_pools');
    }
};
