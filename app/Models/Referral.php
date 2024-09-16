<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_id',
        'referral_invited',
        'line',
        //'parent_id',
        'is_action',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Пользователь пригласивший другого пользователя
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Пользователь приглашенный по реферальной программе
     */
    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }
}
