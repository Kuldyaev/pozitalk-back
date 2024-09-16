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
        Schema::create('academy_payed', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->cascadeOnDelete()->constrained();
            $table->foreignIdFor(\App\Models\AcademyCourse::class)->cascadeOnDelete()->constrained();
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
        Schema::dropIfExists('academy_payed');
    }
};
