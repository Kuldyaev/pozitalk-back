<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('users')->insert($this->getData());
    }

    private function getData()
    {
        return [
            [ 
                "id" => 1,
                "name" =>'admin',
                "familyname"=>'super',
                "phone"=>'79200145400',
                "usersrole_id"=>2,
                "avatar"=>null
            ],
        ];
    }
}
