<?php

use App\Models\AcademyCourseItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('academy_course_item_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AcademyCourseItem::class, 'item_id');
            $table->string('file');
            $table->string('type');
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academy_course_item_files');
    }
};
