<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArbBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'can_pay',
        'cumulative_program',
        'children_program',
        'pension_program',
    ];
}
