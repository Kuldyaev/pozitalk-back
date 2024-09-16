<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seling extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'member_id',
        'sum',
        'product_id',
        'line',
        'date'
    ];
}
