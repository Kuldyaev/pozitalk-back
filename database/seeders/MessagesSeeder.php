<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('messages')->insert($this->getData());
    }

    private function getData()
    {
        return [
            [ 
                "chat_id" => 1,
                "user_id" => 2,
                "body" =>"Добрый день"
            ],
            [ 
                "chat_id" => 1,
                "user_id" => 5,
                "body" =>"Здравствуйте!"
            ],
              [ 
                "chat_id" => 1,
                "user_id" => 2,
                "body" =>"Сегодня в 14:00 мск удобно созвониться?"
              ],
             [ 
                "chat_id" => 1,
                "user_id" => 5,
                "body" =>"Конечно!"
            ], 
             [ 
                "chat_id" => 2,
                "user_id" => 3,
                "body" =>"Добрый день"
            ],
            [ 
                "chat_id" => 2,
                "user_id" => 5,
                "body" =>"Здравствуйте!"
            ],
              [ 
                "chat_id" => 2,
                "user_id" => 3,
                "body" =>"Сегодня в 11:00 мск удобно созвониться?"
              ],
             [ 
                "chat_id" => 2,
                "user_id" => 5,
                "body" =>"К сожалению это время неудобно"
            ], 
        ];
    }   
}

