<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="QuestionAnswer",
 *     description="QuestionAnswer model",
 *     @OA\Xml(
 *         name="QuestionAnswer"
 *     )
 * )
 */
class QuestionAnswer extends Model
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
     *     title="question",
     *     description="question",
     *     format="string",
     *     example="test"
     * )
     */
    private $question;

    /**
     * @OA\Property(
     *     title="answer",
     *     description="answer",
     *     format="string",
     *     example="test"
     * )
     */
    private $answer;

    /**
     * @OA\Property(
     *     title="sort",
     *     description="sort",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $sort;

    protected $fillable = [
        'question',
        'answer',
        'sort',
    ];
}
