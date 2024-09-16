<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArbDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'count_months',
        'percent',
        'start',
        'is_active',
        'is_can_request',
        'is_request',
        'is_wont_request'
    ];
}
