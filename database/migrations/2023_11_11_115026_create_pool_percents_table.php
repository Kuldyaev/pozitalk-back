<?php

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
        Schema::create('pool_percents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('key');
            $table->string('percent');
            $table->timestamps();
        });

        $data = [
            [
                'title' => 'ARB 5 (еженедельное начисление по депозиту)',
                'key' => 'arb_com_5',
                'percent' => '5',
            ],
            [
                'title' => 'ARB 6 (еженедельное начисление по депозиту)',
                'key' => 'arb_com_6',
                'percent' => '6',
            ],
            [
                'title' => 'ARB 7 (еженедельное начисление по депозиту)',
                'key' => 'arb_com_7',
                'percent' => '7',
            ],

            [
                'title' => 'ARB Партнерский пул 0.5% (недельный)',
                'key' => 'pool-arb-plat',
                'percent' => '0.00125',
            ],

            [
                'title' => 'Партнерский пул 3% (ежедневный)',
                'key' => 'pool',
                'percent' => '3',
            ],

            [
                'title' => 'Dexnet (за 9+)',
                'key' => 'pool-2',
                'percent' => '2',
            ],
            [
                'title' => 'Dexnet (за 29+)',
                'key' => 'pool-3',
                'percent' => '2',
            ],
            [
                'title' => 'Dexnet (за 69+)',
                'key' => 'pool-5',
                'percent' => '3',
            ],

            [
                'title' => 'Пул Товарооборот (от 100 000)',
                'key' => 'founder1',
                'percent' => '2',
            ],
            [
                'title' => 'Пул Товарооборот (от 250 000)',
                'key' => 'founder2',
                'percent' => '2',
            ],
            [
                'title' => 'Пул Товарооборот (от 500 000)',
                'key' => 'founder3',
                'percent' => '2',
            ],
            [
                'title' => 'Пул Товарооборот (от 1 000 000)',
                'key' => 'founder4',
                'percent' => '2',
            ],

            [
                'title' => 'ARB пул 1 (от 5 тысяч / еженедельный)',
                'key' => 'pool-arb-1',
                'percent' => '0.0025',
            ],
            [
                'title' => 'ARB пул 2 (от 15 тысяч / еженедельный)',
                'key' => 'pool-arb-2',
                'percent' => '0.0025',
            ],
            [
                'title' => 'ARB пул 3 (от 25 тысяч / еженедельный)',
                'key' => 'pool-arb-3',
                'percent' => '0.0025',
            ],
            [
                'title' => 'ARB пул 4 (от 50 тысяч / еженедельный)',
                'key' => 'pool-arb-4',
                'percent' => '0.0025',
            ],
            [
                'title' => 'ARB пул 5 (от 100 тысяч / еженедельный)',
                'key' => 'pool-arb-5',
                'percent' => '0.0025',
            ],
        ];

        DB::table('pool_percents')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_percents');
    }
};
