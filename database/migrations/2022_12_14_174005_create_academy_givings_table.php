<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('academy_givings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('date');
            $table->string('zoom_url');
            $table->string('youtube_url');
        });

        DB::table('academy_givings')->insert([
            'title' => 'Академия мастерства жизни',
            'description' => 'Обучение от Виталия Бринкманна и инициаторов движения. Академия мастерства жизни — это полный день обучения на важные для каждого темы. Знания и мудрость, которые позволят достичь желаемого с максимальным удовольствием и в минимальные сроки.',
            'date' => Carbon::now(),
            'zoom_url' => 'https://www.google.ru/',
            'youtube_url' => 'https://www.google.ru/',
        ]);
        DB::table('academy_givings')->insert([
            'title' => 'валюта жизни',
            'description' => 'Базовые знания жизни, которые позволяют разобраться с вопросами: Как обеспечить себе доход, который позволит заниматься тем, что нравится? Как прокачать сферу отношений и восполнить недостаток душевного общения? Как быть честным с собой и искренним с другими?',
            'date' => Carbon::now(),
            'zoom_url' => 'https://www.google.ru/',
            'youtube_url' => 'https://www.google.ru/',
        ]);
        DB::table('academy_givings')->insert([
            'title' => 'Обучение GiftPool CLUB',
            'description' => 'Занятия со специально приглашенными гостями, которые максимально облегчат вам взаимодействие с личным кабинетом GiftPool Club. На этих занятия мы разбираем любые темы: Работа с криптовалютами, простота общения с другими пользователями, возможности личного кабинета.',
            'date' => Carbon::now(),
            'zoom_url' => 'https://www.google.ru/',
            'youtube_url' => 'https://www.google.ru/',
        ]);
        DB::table('academy_givings')->insert([
            'title' => 'Презентация giftpool club',
            'description' => 'На презентации вы сможете познакомиться с проектом, и познакомить с ним ваших близких и знакомых. Вы узнаете как работает платформа, а также узнаете миссию ради которой был создан проект GiftPool Club. Презентация — самые простой способ быстро получить ответы на все ваши вопросы в режиме конференции, где у каждого будет возможность лично задать вопрос инициаторам платформы.',
            'date' => Carbon::now(),
            'zoom_url' => 'https://www.google.ru/',
            'youtube_url' => 'https://www.google.ru/',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academy_givings');
    }
};
