<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     title="LessonRecord",
 *     description="Записи занятий",
 *     @OA\Xml(
 *         name="LessonRecord"
 *     )
 * )
 */
class LessonRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'link',
        'price'
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

    /**
     * @OA\Property(
     *     title="price",
     *     description="Цена",
     *     format="integer",
     *     example=51.45
     * )
     */
    private $price;

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
    ];

    public function toArray()
    {
        if( auth()->user()->role_id != 1 ){
            return parent::toArray();
        }
        $payed = LessonPayed::where([
            'user_id'=>auth()->user()->id,
            'lesson_record_id'=>$this->getAttribute('id')
        ])->first();

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
}
