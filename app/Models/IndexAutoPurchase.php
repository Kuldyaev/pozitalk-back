<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexAutoPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'error_code',
        'amount',
        'wallet_id',
        'is_active',
        'regularity',
    ];

    public static function updateByParams($user_id, $program_id, $regularity, $error_code, $is_active = false)
    {
        self::where('user_id', $user_id)
            ->where('program_id', $program_id)
            ->where('regularity', $regularity)
            ->update(['error_code' => $error_code, 'is_active' => $is_active]);
    }
}
