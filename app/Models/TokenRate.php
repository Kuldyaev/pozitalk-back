<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="TokenRate",
 *     description="Курс токена",
 *     @OA\Xml(
 *         name="TokenRate"
 *     )
 * )
 */
class TokenRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'private_rate',
        'classic_rate',
    ];
}
