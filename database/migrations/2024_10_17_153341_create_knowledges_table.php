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
        Schema::create('knowledges', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('author', 200);
            $table->string('date', 12);
            $table->text('image');
            $table->text('description');
            $table->text('text');
            $table->string('time_publish', 8);
            $table->string('date_publish', 12);
            $table->boolean('age16_restriction');
            $table->boolean('age18_restriction');
            $table->integer('reading_time');
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
        Schema::dropIfExists('knowledges');
    }
};
