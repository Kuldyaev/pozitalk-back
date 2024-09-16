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
        $data = [
            [
                'title' => 'ia-system (еженедельное начисление по депозиту)',
                'key' => 'ia-system-deposit',
                'percent' => '1',
            ],
            [
                'title' => 'ia-system (еженедельное начисление по пулам)',
                'key' => 'ia-system-pool',
                'percent' => '0.5',
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
        //
    }
};
