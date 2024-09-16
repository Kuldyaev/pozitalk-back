<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @OA\Schema(
 *     title="AcademyCourseCategory",
 *     description="Категории курсов академии",
 *     @OA\Xml(
 *         name="AcademyCourseCategory"
 *     )
 * )
 */
class AcademyCourseCategory extends Model
{
    use HasFactory;

    protected $table = "academy_course_categories";

    protected $fillable = [
        'name',
        'tags',
        'zoom',
        'test',
        'date',
        'url',
        'is_cycle',
        'is_deutsche',
        'is_capital',
        'direction',
        'short_description',
        'access',
        'image',
        'sort_order',
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
     *     title="tags",
     *     description="Описание курса транслитирированный",
     *     format="json",
     *     example="Живое участие в зум, записи, что-то еще"
     * )
     */
    private $tags;

    /**
     * @OA\Property(
     *     title="zoom",
     *     description="zoom",
     *     format="bool",
     *     example=true
     * )
     */
    private $zoom;

    /**
     * @OA\Property(
     *     title="test",
     *     description="Тест родители это святое",
     *     format="bool",
     *     example=true
     * )
     */
    private $test;

    protected $casts = [
        'tags' => 'array',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(AcademyCourse::class, 'academy_course_category_id', 'id');
    }

    public function latestViews(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'latest_categories_users', 'category_id', 'user_id')
            ->orderBy('id');
    }
}
