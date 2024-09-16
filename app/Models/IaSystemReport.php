<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaSystemReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sum',
        'count_pay',
        'type',
    ];
}
