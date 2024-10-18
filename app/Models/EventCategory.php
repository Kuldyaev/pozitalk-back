<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Knowledge",
 *     description="Knowledge model",
 *     @OA\Xml(
 *         name="Knowledge"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="EventCategoty ID"
 *     ),
 *     @OA\Property(
 *         property="event_category",
 *         type="string",
 *         description="Name of EventCategoty"
 *     )
 * )
 *  * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int $id
 */


class EventCategory extends Model
{
    protected $table = 'event_categories';

    protected $fillable = [
        'event_category',
    ];
}
