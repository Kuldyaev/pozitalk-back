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
        Schema::table('academy_course_categories', function (Blueprint $table) {
            $table->text('direction')->nullable();
            $table->text('short_description')->nullable();
            $table->text('access')->nullable();
            $table->text('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academy_course_categories', function (Blueprint $table) {
            $table->dropColumn("direction");
            $table->dropColumn("short_description");
            $table->dropColumn('access');
            $table->dropColumn('image');
        });
    }
};
