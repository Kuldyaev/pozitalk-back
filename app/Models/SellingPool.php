<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellingPool extends Model
{
    use HasFactory;

    protected $fillable = [
        'selling_id',
        'key',
        'sum',
        'participants',
        'sum_per_participant',
    ];
}
