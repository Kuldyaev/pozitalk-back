<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoundGiverPay extends Model
{
    use HasFactory;

    protected $fillable = [
        'giver_id',
        'round_id',
        'is_payed',
    ];
}
