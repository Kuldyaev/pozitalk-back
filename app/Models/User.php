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
 *         property="login",
 *         type="string",
 *         description="User's login"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="User's email address"
 *     ),
 *     @OA\Property(
 *         property="role_id",
 *         type="integer",
 *         description="User's role ID"
 *     ),
 *     @OA\Property(
 *         property="status_id",
 *         type="integer",
 *         description="User's status ID"
 *     ),
 *     @OA\Property(
 *         property="telegram_name",
 *         type="string",
 *         description="User's Telegram username"
 *     ),
 *     @OA\Property(
 *         property="show_welcome",
 *         type="boolean",
 *         description="Flag indicating whether to show the welcome message"
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="User's message"
 *     ),
 *     @OA\Property(
 *         property="active_queue",
 *         type="integer",
 *         description="User's active queue"
 *     ),
 *     @OA\Property(
 *         property="wallet",
 *         type="string",
 *         description="User's wallet information"
 *     ),
 *     @OA\Property(
 *         property="commission",
 *         type="string",
 *         description="User's commission details"
 *     ),
 *     @OA\Property(
 *         property="token_stacking",
 *         type="string",
 *         description="User's token stacking information"
 *     ),
 *     @OA\Property(
 *         property="token_vesting",
 *         type="string",
 *         description="User's token vesting information"
 *     ),
 *     @OA\Property(
 *         property="security_question",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Array of security questions"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was last updated"
 *     ),
 *      @OA\Property(
 *          property="founder_status",
 *          type="integer",
 *          description="Founder status"
 *      ),
 * )
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property int $id
 * @property string $login
 * @property int $referal_id
 * @property string $referal_invited
 * @property string|null $email
 * @property string $password
 * @property Collection|null $authProviders
 * @property float $wallet
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

     const POOLS_STATUS = [
        'bronze' => '1%',
        'silver' => '1%',
        'gold' => '1%',
        'platinum' => '3%',
        'founder1' => '2%',
        'founder2' => '2%',
        'founder3' => '2%',
        'founder4' => '2%',
    ];


    protected $fillable = [
        'login',
        'email',
        'email_verified_at',
        'phone_verified_at',
        'telegram_id',
        'phone',
        'avatar',
        'name',
        'surname',
        'gender',
        'event_country',
        'event_city',
        'role_id',
        'status_id',
        'telegram_name',
        'show_welcome',
        'message',
        'active_queue',
        'wallet',
        'commission', // Others statuses (0.3 - basic, 0.5 - bornze, 0.7 - silver, 1 - gold and platinum)
        'token_stacking',
        'token_vesting',
        'security_question',
        'founder_status', // Osnovatel
        'referal_invited',
        'telegram_policy',
        'level_tiered_system',
    ];


    protected $hidden = [
        'code',
        'remember_token',
        'password',
    ];



    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'security_question' => 'array',
        'telegram_policy' => UserTelegramPolicyEnum::class,
    ];

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
