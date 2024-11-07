<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('usersrole')->insert($this->getData());
    }

    private function getData()
    {
        return [
            [ 
                "id" => 1,
                "role" =>'superadmin'
            ],
             [ 
                "id" => 2,
                "role" =>'admin'
            ],
             [ 
                "id" => 3,
                "role" =>'specialist'
            ],
             [ 
                "id" => 4,
                "role" =>'client'
            ]
        ];
    }
}
