<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Auth\AuthProvider;
use App\Models\User\UserTelegramPolicyEnum;
use IEXBase\TronAPI\Tron;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\UsdtWallet;
use Illuminate\Database\Eloquent\Collection;

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
 *         property="email_verified_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user's email was verified"
 *     ),
 *     @OA\Property(
 *         property="phone_verified_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user's phone was verified"
 *     ),
 *     @OA\Property(
 *         property="telegram_id",
 *         type="string",
 *         description="User's Telegram ID"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="User's phone number"
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
    use Notifiable;
    use HasApiTokens;

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

    public function getStatus(): string
    {
        if (auth()->user()->founder_status == 0) {
            $userStatus = UsdtTransaction::where('user_id', auth()->user()->id)
                ->where(function ($query) {
                    $query->where('product', 'bronze')
                        ->orWhere('product', 'silver')
                        ->orWhere('product', 'gold')
                        ->orWhere('product', 'platinum');
                })
                ->orderBy('id', 'desc')
                ->first()->product ?? 'base';
        } else {
            $userStatus = 'founder' . auth()->user()->founder_status;
        }

        return $userStatus;
    }

    public function getUserPools($userStatus): array
    {
        $keys = array_keys(self::POOLS_STATUS);
        $startIndex = array_search($userStatus, $keys);
        $filteredKeys = array_filter($keys, function ($key, $index) use ($startIndex) {
            return $index <= $startIndex;
        }, ARRAY_FILTER_USE_BOTH);

        return array_intersect_key(self::POOLS_STATUS, array_flip($filteredKeys));
    }


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

    public function getAvatarAttribute(?string $value): ?string
    {
        return $value ? config("filesystems.dirs.user.avatar") . $value : null;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar
            ? Storage::disk('public')->url($this->avatar)
            : null;
    }

    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(UserAccount::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'referal_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referal_id');
    }

    public function cryptoWallets(): HasMany
    {
        return $this->hasMany(UserCryptoWallet::class);
    }

    public function parentRef(): HasMany
    {
        return $this->hasMany(Referral::class, 'user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referral_id');
    }

    /**
     * Владелец платежа
     */
    public function reportReferrals(): HasMany
    {
        return $this->hasMany(ReportReferral::class, 'owner_id');
    }

    public function reportReferralsFolowed(): HasMany
    {
        return $this->hasMany(ReportReferral::class, 'member_id');
    }

    public function selings(): HasMany
    {
        return $this->hasMany(Seling::class, 'member_id');
    }

    public function selingsWithoutDeposit(): HasMany
    {
        return $this->hasMany(Seling::class, 'member_id')
            ->where('product_id', '!=', 'arb_deposit');
    }

    public function getTotalsAttribute(): array
    {
        $straight = User::query()
            ->where('referal_id', $this->id)
            ->get();

        $total = 0;
        $straight_total = 0;
        if ($straight) {
            $ids = [];
            foreach ($straight as $u) {
                $ids[] = $u->id;
            }
            $straight_total += count($ids);

            while (true) {
                if ($ids) {
                    $users = User::whereIn('referal_id', $ids)->get();
                    if ($users) {
                        $ids = [];
                        foreach ($users as $u) {
                            $ids[] = $u->id;
                        }
                        $total += count($ids);
                    } else
                        break;
                } else
                    break;
            }
        }

        return [
            'straight' => $straight_total,
            'total' => $total + $straight_total,
        ];
    }

    public function getUsdtWallet($product = null)
    {
        $wallet = UsdtWallet::where('user_id', $this->id)->where('product', $product)->first();

        if ($wallet) {
            $wallet->date = date('Y-m-d H:i:s');
            $wallet->save();
            return $wallet->wallet;
        } else {
            $tron = new Tron();

            $generateAddress = $tron->generateAddress(); // or createAddress()
            $isValid = $tron->isAddress($generateAddress->getAddress());
            if (!$isValid)
                $generateAddress = $tron->generateAddress();
            $addr = new UsdtWallet();
            $addr->user_id = $this->id;
            $addr->wallet = $generateAddress->getAddress(true);
            $addr->private_key = $generateAddress->getPrivateKey();
            $addr->public_key = $generateAddress->getPublicKey();
            $addr->contract_address = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
            $addr->date = date('Y-m-d H:i:s');
            $addr->active = 0;
            $addr->product = $product;
            $addr->save();
            return $addr->wallet;
        }
    }

    public function getUsdtWalletQr($product = null)
    {
        return 'https://chart.googleapis.com/chart?cht=qr&chs=260x260&chl=' . $this->getUsdtWallet($product);
    }

    public function latestCourseCategories(): BelongsToMany
    {
        return $this->belongsToMany(AcademyCourseCategory::class, 'latest_categories_users', 'user_id', 'category_id')
            ->orderByPivot('id', 'desc');
    }
}
