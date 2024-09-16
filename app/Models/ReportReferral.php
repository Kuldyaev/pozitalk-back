<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Model|null $product
 * @property array|null $data
 */
class ReportReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'member_id',
        'sum',
        'line',
        'comment',
        'type',
        'product_id',
        'product_type',
        'data',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Владелец платежа
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Участник платежа
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function product(): MorphTo
    {
        return $this->morphTo('product');
    }
}
