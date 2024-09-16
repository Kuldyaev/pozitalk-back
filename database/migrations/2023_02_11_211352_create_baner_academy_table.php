<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baner_academy', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('name');
            $table->text('description');
            $table->string('fio');
            $table->string('image');
            $table->double('price');
            $table->timestamps();
        });
        DB::table('baner_academy')->insert([
            'date'=>Carbon::now(),
            'name'=>'Имя',
            'description'=>'Описание',
            'fio'=>'Фамилия Имя Отчество',
            'image'=>'path/to/image.jpg',
            'price'=>10,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baner_academy');
    }
};
