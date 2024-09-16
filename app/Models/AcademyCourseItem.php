<?php

namespace App\Models;

use Vi\Models\Academy\AcademyCourseItemMoment;
use Vi\Models\Academy\AcademyCourseItemFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     title="AcademyCourseItem",
 *     description="Урок академии",
 *     @OA\Xml(
 *         name="AcademyCourseItem"
 *     )
 * )
 */
class AcademyCourseItem extends Model
{
    use HasFactory;

    protected $table = "academy_course_items";

    protected $fillable = [
        'name',
        'link',
        'academy_course_id',
        'timecodes',
        'preview_image'
    ];


    /**
     * @OA\Property(
     *     title="id",
     *     description="id",
     *     format="int64",
     *     example=1,
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
     *     title="link",
     *     description="link",
     *     format="string",
     *     example="vk.com/1"
     * )
     */
    private $link;

    /**
     * @OA\Property(
     *     title="academy_course_id",
     *     description="academy_course_id",
     *     format="integer",
     *     example=1
     * )
     */
    private $academy_course_id;

    /**
     * @OA\Property(
     *     title="timecodes",
     *     description="Таймкоды",
     *     format="json",
     *     example="Живое участие в зум, записи, что-то еще"
     * )
     */
    private $timecodes;

    protected $casts = [
        'timecodes' => 'array',
    ];

    public function toArray()
    {
        if (auth()->user()->role_id != 1) {
            return parent::toArray();
        }
        $academy = AcademyCourse::where('id', $this->getAttribute('academy_course_id'))->first();

        if ($academy->subscription_cost != null || $academy->subscription_cost > 0) {
            $sub = AcademySubscribe::where('user_id', auth()->user()->id)->where('academy_course_id', $academy->id)->orderBy('created_at', 'desc')->first();

            if (isset($sub) && $sub->is_active) {
                return parent::toArray();
            }
        }

        if ($this->getAttribute('academy_course_id') == 49) {
            $rep = BannerPayed::where('product_id', 'banner_academy_1678695067500')
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($rep) {
                return parent::toArray();
            }
        }
        if ($this->getAttribute('academy_course_id') == 54) {
            $rep = BannerPayed::where('product_id', 'banner_academy_1681102414973')
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($rep) {
                return parent::toArray();
            }
        }


        if ($academy->price == 0) {
            return parent::toArray();
        }

        $payed = AcademyPayed::where([
            'user_id' => auth()->user()->id,
            'academy_course_id' => $this->getAttribute('academy_course_id')
        ])->first();


        if ($payed) {
            return parent::toArray();
        }

        // проверка куплен или нет
        if (!$payed) {
            $this->setAttribute('link', null);
        }


        return parent::toArray();
    }

    public function setAttributeVisibility()
    {
        $this->makeVisible(array_merge($this->fillable, $this->appends, ['link']));
    }

    public function files(): HasMany
    {
        return $this->hasMany(AcademyCourseItemFile::class, 'item_id');
    }

    public function moments(): HasMany
    {
        return $this->hasMany(AcademyCourseItemMoment::class, 'item_id');
    }
}
