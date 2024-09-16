<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     title="AcademyCourse",
 *     description="Курсы академии",
 *     @OA\Xml(
 *         name="AcademyCourse"
 *     )
 * )
 */
class AcademyCourse extends Model
{
    use HasFactory;

    protected $with = [
        'items'
    ];

    protected $fillable = [
        'name',
        'academy_course_category_id',
        'type',
        'type_translated',
        'description',
        'gift',
        'preview_image',
        'price',
        'tokens',
        'subscription_cost',
        'subscription_cost_first',
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
     *     description="name",
     *     format="string",
     *     example=1
     * )
     */
    private $name;

    /**
     * @OA\Property(
     *     title="academy_course_category_id",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     */
    private $academy_course_category_id;



    /**
     * @OA\Property(
     *     title="type",
     *     description="Тип курса",
     *     format="string",
     *     example="Тренинг"
     * )
     */
    private $type;

    /**
     * @OA\Property(
     *     title="price",
     *     description="Цена",
     *     format="integer",
     *     example=51.45
     * )
     */
    private $price;

    /**
     * @OA\Property(
     *     title="type_translated",
     *     description="Тип курса транслитирированный",
     *     format="string",
     *     example="Trening"
     * )
     */
    private $type_translated;

    /**
     * @OA\Property(
     *     title="description",
     *     description="Описание курса транслитирированный",
     *     format="string",
     *     example="Описание"
     * )
     */
    private $description;

    /**
     * @OA\Property(
     *     title="gift",
     *     description="Месяц когда курс дарится 0 - никогда, 1 - январь 2-февраль...",
     *     format="int64",
     *     example=1
     * )
     */
    private $gift;

    public function items(): HasMany
    {
        return $this->hasMany(AcademyCourseItem::class, 'academy_course_id', 'id');
    }
}
