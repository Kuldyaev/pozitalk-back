<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="GiftClub",
 *     description="GiftClub model",
 *     @OA\Xml(
 *         name="GiftClub"
 *     )
 * )
 */
class GiftClub extends Model
{
    use HasFactory;

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $id;

    /**
     * @OA\Property(
     *     title="title",
     *     description="title",
     *     format="string",
     *     example="test"
     * )
     */
    private $title;

    /**
     * @OA\Property(
     *     title="url",
     *     description="url",
     *     format="string",
     *     example="test"
     * )
     */
    private $url;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'url',
        'expire_academy',
        'date',
        'duration',
        'is_actual'
    ];

    protected $casts = [
        'expire_academy' => 'datetime:Y-m-d H:i:s',
    ];
}
