<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property User $user
 * @property AuthProviderStatusEnum $status
 * @property AuthProviderEnum $provider
 * @property array $data
 */
class AuthProvider extends Model
{

    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'status',
        'data'
    ];

    protected $casts = [
        'data' => 'json',
        'provider' => AuthProviderEnum::class,
        'status' => AuthProviderStatusEnum::class,
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->id = uuid_create();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
