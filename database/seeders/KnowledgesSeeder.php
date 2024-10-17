<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KnowledgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('knowledges')->insert($this->getData());
    }

    private function getData()
    {
        return [
            [   "title" => "Название",
                "author" => "Автор",
                "date"=>"2024-10-15",
                "image"=>"thgreg",
                "description"=>"description",
                "text"=>"text",
                "time_publish"=>"12:00",
                "date_publish"=>"2024-10-18",
                "age16_restriction"=>FALSE,
                "age18_restriction"=>FALSE,
            ]
        ];
    }    
}
