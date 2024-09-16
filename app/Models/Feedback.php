<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Feedback",
 *     description="Feedback model",
 *     @OA\Xml(
 *         name="Feedback"
 *     )
 * )
 */
class Feedback extends Model
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
     *     title="user_id",
     *     description="user_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="status_id",
     *     description="status_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $status_id;

    /**
     * @OA\Property(
     *     title="message",
     *     description="message",
     *     format="string",
     *     example="test"
     * )
     *
     */
    private $message;

    /**
     * @OA\Property(
     *     title="image",
     *     description="image",
     *     format="file",
     *     example="test"
     * )
     *
     */
    private $image;

    /**
     * @OA\Property(
     *     title="comment",
     *     description="comment",
     *     format="string",
     *     example="test"
     * )
     *
     */
    private $comment;

    /**
     * @OA\Property(
     *     title="created_at",
     *     description="дата создания записи",
     *     format="date",
     *     example="2019-05-17"
     * )
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="updated_at",
     *     description="дата изменения записи",
     *     format="date",
     *     example="2019-05-17"
     * )
     */
    private $updated_at;

    protected $fillable = [
        'user_id',
        'status_id',
        'message',
        'image',
        'comment',
    ];
}
