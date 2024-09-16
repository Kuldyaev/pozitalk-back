<?php

use App\Models\AcademyCourseItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('academy_course_item_moments', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->string('link')->nullable();
            $table->foreignIdFor(AcademyCourseItem::class, 'item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('academy_course_item_moments');
    }
};
