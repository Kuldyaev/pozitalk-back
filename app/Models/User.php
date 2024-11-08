<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;



/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(
 *         name="User"
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="User ID"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="User's name"
 *     ),
  *     @OA\Property(
 *         property="familyname",
 *         type="string",
 *         description="User's familyname"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         format="email",
 *         description="User's phone number"
 *     ),
 *     @OA\Property(
 *         property="avatar",
 *         type="text",
 *         format="base64",
 *         description="avatar in base64"
 *     ),
 * )
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int $id
 * @property string $login
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'phone',
        'name',
        'familyname',
        'avatar',
        'usersrole_id',

    ];


    public function role()
    {
        return $this->belongsTo(UserRole::class, 'usersrole_id');
    }

    protected $hidden = [
        'code',
      ];

        // Показывает список всех пользователей


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
