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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('surname', 200);
            $table->string('lastname', 200);
            $table->string('birth_date', 15);
            $table->string('phone_number', 15);
            $table->string('email', 200);
            $table->string('education', 250);
            $table->integer('rate_hour');
            $table->boolean('iswoman');
            $table->text('avatar');
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
        Schema::dropIfExists('applications');
    }
};
