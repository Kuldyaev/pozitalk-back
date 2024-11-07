<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
         $this->call(KnowledgesSeeder::class);
         $this->call(EventCategorySeeder::class);
         $this->call(MessagesSeeder::class);
         $this->call(ApplicationSeeder::class);
         $this->call(UsersRoleTableSeeder::class);
         $this->call(UsersSeeder::class);
         // \App\Models\User::factory(10)->create();
    }
}
