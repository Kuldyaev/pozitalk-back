<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->tinyInteger("telegram_policy")->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropColumns("users", 'telegram_policy');
    }
};
