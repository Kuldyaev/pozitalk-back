<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Application",
 *     description="Application model",
 *     @OA\Xml(
 *         name="Application"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Application ID"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Applicant name"
 *     ),
 *     @OA\Property(
 *         property="surname",
 *         type="string",
 *         description="Applicant surname"
 *     ),
 *     @OA\Property(
 *         property="lastname",
 *         type="string",
 *         description="Applicant lastname"
 *     ),
 *     @OA\Property(
 *         property="avatar",
 *         type="text",
 *         description="avatar base64"
 *     ),
 *     @OA\Property(
 *         property="education",
 *         type="string",
 *         description="info about education"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="email"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         description="birth_date"
 *     ),
 *     @OA\Property(
 *         property="phone_number",
 *         type="string",
 *         description="phone_number"
 *     ),
 *     @OA\Property(
 *         property="rate_hour",
 *         type="integer",
 *         description="rate_hour"
 *     ),
 *     @OA\Property(
 *         property="iswoman",
 *         type="boolean",
 *         description="gender info"
 *     ),
 * )
 *  * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int $id
 */

class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'name',
        'surname',
        'lastname',
        'birth_date',
        'phone_number',
        'email',
        'education',
        'rate_hour',
        'iswoman',
        'avatar',
    ];
}
