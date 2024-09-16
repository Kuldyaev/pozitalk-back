<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenVestingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'count',
        'type',
        'usdt',
        'balance',
    ];
}