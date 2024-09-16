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
        Schema::table('round_givers', function (Blueprint $table) {
            $table->boolean('is_distributed')->default(false);
            $table->integer('is_congratulated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('round_givers', function (Blueprint $table) {
            $table->dropColumn("is_distributed");
            $table->dropColumn("is_congratulated");
        });
    }
};
