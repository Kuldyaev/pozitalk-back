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
        Schema::table('gift_clubs', function (Blueprint $table) {
            $table->text('date')->nullable();
            $table->text('duration')->nullable();
            $table->boolean('is_actual')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academy_course_items', function (Blueprint $table) {
            $table->dropColumn("date");
            $table->dropColumn("duration");
            $table->dropColumn("is_actual");
        });
    }
};
