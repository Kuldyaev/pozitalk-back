<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('applications')->insert($this->getData());
    }

    private function getData()
    {
        return [
            [ 
                "name" => "Адриан",
                "surname" => "Михайлович",
                "lastname" =>"Пиотровский",
                "birth_date" => "05.09.1956",
                "phone_number" => "79203201868",
                "email" =>"viacheslav@kuldyaev.ru",
                "education" => "Лесотехнический техникум",
                "rate_hour" => 2000,
                "iswoman" =>false,
                "avatar" =>"data:image/jpg;base64,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",

            ],
          
        ];
    }   
}







