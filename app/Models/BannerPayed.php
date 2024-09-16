<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerPayed extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id'
    ];
    protected $table='banner_payed';
}