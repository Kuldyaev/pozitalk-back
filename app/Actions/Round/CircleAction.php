<?php
namespace App\Actions\Round;

use Illuminate\Http\Request;

class CircleAction
{
    public static function get($rounds)
    {
        $circle = 1;
        if(isset($rounds[1])
            && $rounds[0]['round_type_id'] == $rounds[1]['round_type_id']
            && isset($rounds[2])
            && $rounds[0]['round_type_id'] == $rounds[2]['round_type_id'])
            $circle = 3;
        elseif(isset($rounds[1])
            && $rounds[0]['round_type_id'] == $rounds[1]['round_type_id'])
            $circle = 2;

        return $circle;
    }
}
