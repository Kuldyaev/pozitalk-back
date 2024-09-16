<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Selling extends Model
{
    use HasFactory;

    protected $fillable = [
        'sum',
        'date',
    ];

    public function pools(): HasMany
    {
        return $this->hasMany(SellingPool::class);
    }
}
