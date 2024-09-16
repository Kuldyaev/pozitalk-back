<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     title="BanerAcademy",
 *     description="Баннер академии",
 *     @OA\Xml(
 *         name="BanerAcademy"
 *     )
 * )
 */
class BanerAcademy extends Model
{
    use HasFactory;

    protected $table = 'baner_academy';

    protected $fillable = [
        'date',
        'name',
        'description',
        'fio',
        'image',
        'price',
        'link',
        'product_id'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
    ];


    public function toArray()
    {
        if( auth()->user()->role_id != 1 ){
            return parent::toArray();
        }
        $payed = BannerPayed::where([
            'user_id'=>auth()->user()->id,
//            'product_id'=>BanerAcademy::first()->product_id
            'product_id'=>$this->getAttribute('product_id')
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
