<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MoneyWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_address',
        'amount',
        'status',
        'coin',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

//    public function getStatusAttribute($value)
//    {
//        return [
//            1 => 'Создано',
//            2 => 'Выполнено',
//            3 => 'Отклонено',
//        ][$value];
//    }
}
