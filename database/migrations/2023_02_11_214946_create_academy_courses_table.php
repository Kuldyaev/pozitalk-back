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
        Schema::create('academy_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\AcademyCourseCategory::class)->cascadeOnDelete()->constrained();
            $table->string('name');
            $table->string('type');
            $table->string('type_translated');
            $table->text('description');
            $table->integer('gift');
            $table->integer('price');
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
        Schema::dropIfExists('academy_courses');
    }
};
