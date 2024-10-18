<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('event_categories')->insert($this->getData());
    }

    private function getData()
    {
        return [
            [ "event_category" => "Обновление сайта"  ],
            [ "event_category" => "Технические работы" ]
        ];
    }   
}
