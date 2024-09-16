<?php

use App\Models\AcademyCourseCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('latest_categories_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(AcademyCourseCategory::class, 'category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('latest_categories_users');
    }
};