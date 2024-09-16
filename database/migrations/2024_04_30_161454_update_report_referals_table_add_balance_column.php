<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('report_referrals', function (Blueprint $table) {
            $table->json('data')->nullable();
            $table->nullableMorphs('product');
        });
    }

    public function down(): void
    {
        Schema::dropColumns('report_referrals', [
            'data',
            'product_type',
            'product_id'
        ]);
    }
};
