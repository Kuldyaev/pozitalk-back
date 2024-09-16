<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="CountUserReferal",
 *     description="CountUserReferal model",
 *     @OA\Xml(
 *         name="CountUserReferal"
 *     )
 * )
 */
class CountUserReferal extends Model
{
    use HasFactory;


    private $id;

    protected $fillable = [
        'user_id',
        'line_one',
        'line_two',
        'line_three',
        'line_four',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
