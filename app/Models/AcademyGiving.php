<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="AcademyGiving",
 *     description="AcademyGiving model",
 *     @OA\Xml(
 *         name="AcademyGiving"
 *     )
 * )
 */
class AcademyGiving extends Model
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

    /**
     * @OA\Property(
     *     title="date",
     *     description="date",
     *     format="string",
     *     example="test"
     * )
     */
    private $date;

    /**
     * @OA\Property(
     *     title="zoom_url",
     *     description="zoom_url",
     *     format="string",
     *     example="test"
     * )
     */
    private $zoom_url;

    /**
     * @OA\Property(
     *     title="youtube_url",
     *     description="youtube_url",
     *     format="string",
     *     example="test"
     * )
     */
    private $youtube_url;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'date',
        'zoom_url',
        'youtube_url',
    ];
}
