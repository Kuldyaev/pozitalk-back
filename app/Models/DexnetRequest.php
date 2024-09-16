<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DexnetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'region',
        'phone',
        'email',
        'address',
        'is_approved',
    ];
}
