<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'count_index',
        'price_index',
        'start',
        'is_active',
        'program_id'
    ];
}
