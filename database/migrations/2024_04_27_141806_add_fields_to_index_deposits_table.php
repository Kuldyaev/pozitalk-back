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
        Schema::table('index_deposits', function (Blueprint $table) {
            $table->string('result')->nullable();
            $table->string('average_price')->nullable();
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('index_deposits', function (Blueprint $table) {
            $table->dropColumn('result');
            $table->dropColumn('average_price');
            $table->dropColumn('type');
        });
    }
};
