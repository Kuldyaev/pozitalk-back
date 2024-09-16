<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_active',
        'wallet_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}