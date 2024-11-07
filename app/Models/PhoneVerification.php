<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="PhoneVerification",
 *     description="Таблица кодов для авторизации",
 *     @OA\Xml(
 *         name="PhoneVerification"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="phoneCode ID"
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Номер телефона"
 *     ),
  *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         description="пятизначный цифровой код"
 *     ),
 *     @OA\Property(
 *         property="valid_until",
 *         type="timestamp",
 *         description="код валиден до"
 *     ),
 * 
 * )
 */

class PhoneVerification extends Model
{
    use HasFactory;
    
    private $id;

    protected $fillable = [
        'phone',
        'code',
        'valid_until',
    ];

}
