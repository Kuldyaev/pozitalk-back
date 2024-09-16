<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable()->after('name');
            $table->string('gender', 8)->nullable();
            $table->string('event_country', 128)->nullable();
            $table->string('event_city', 128)->nullable()->after('event_country');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('users', [
            'surname',
            'gender',
            'event_country',
            'event_city',
        ]);
    }
};
