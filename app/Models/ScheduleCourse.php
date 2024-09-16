<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     title="ScheduleCourse",
 *     description="Расписание курсов",
 *     @OA\Xml(
 *         name="ScheduleCourse"
 *     )
 * )
 */
class ScheduleCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'link',
        'is_capital',
        'is_for_subscribers',
        'is_big_capital',
        'is_partner',
    ];

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     */
    private $id;

    /**
     * @OA\Property(
     *     title="name",
     *     description="Название",
     *     format="string",
     *     example="Родители —это святое"
     * )
     */
    private $name;

    /**
     * @OA\Property(
     *     title="name",
     *     description="Дата (произвольная строка)",
     *     format="string",
     *     example="30 января в 19:00 по МСК"
     * )
     */
    private $date;

    /**
     * @OA\Property(
     *     title="link",
     *     description="Ссылка на курс",
     *     format="string",
     *     example="https://vk.com/1"
     * )
     */
    private $link;

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
    ];
}
