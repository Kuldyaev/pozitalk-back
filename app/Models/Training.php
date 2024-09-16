<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Training",
 *     description="Training model",
 *     @OA\Xml(
 *         name="Training"
 *     )
 * )
 */
class Training extends Model
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
     *     title="description",
     *     description="description",
     *     format="string",
     *     example="test"
     * )
     */
    private $description;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
    ];
}
