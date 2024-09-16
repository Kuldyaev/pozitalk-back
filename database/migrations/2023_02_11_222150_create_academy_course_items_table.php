<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AcademyCourse;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academy_course_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 500);
            $table->string('link', 2000);
            $table->foreignIdFor(AcademyCourse::class)->cascadeOnDelete()->constrained();
            $table->json('timecodes');
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
        Schema::dropIfExists('academy_course_items');
    }
};
