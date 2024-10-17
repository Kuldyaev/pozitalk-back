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
 *         description="Knowledge ID"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of article"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         description="Author of Acticle"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         description="date writing acticle"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="text",
 *         description="cover base64"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="text",
 *         description="short description"
 *     ),
 *     @OA\Property(
 *         property="text",
 *         type="text",
 *         description="formatted text "
 *     ),
 *     @OA\Property(
 *         property="time_publish",
 *         type="string",
 *         description="time for publishing "
 *     ),
 *     @OA\Property(
 *         property="date_publish",
 *         type="string",
 *         description="date for publishing "
 *     ),
 *     @OA\Property(
 *         property="age16_restriction",
 *         type="boolean",
 *         description="16+"
 *     ),
 *     @OA\Property(
 *         property="age18_restriction",
 *         type="boolean",
 *         description="18+"
 *     ),
 * )
 *  * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int $id
 */

class Knowledge extends Model
{
    protected $table = 'knowledges';

    protected $fillable = [
        'title',
        'author',
        'date',
        'image',
        'description',
        'text',
        'time_publish',
        'date_publish',
        'age16_restriction',
        'age18_restriction'
    ];
}
