<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 *     title="UserAccount",
 *     description="Аккаунт пользователя",
 *     @OA\Xml(
 *         name="UserAccount"
 *     )
 * )
 */
class UserAccount extends Model
{
    use HasFactory;

    /**
     * @OA\Property(
     *     title="ID",
     *     description="ID",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $id;

    /**
     * @OA\Property(
     *     title="user_id",
     *     description="user_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="active",
     *     description="active",
     *     format="boolean",
     *     example=true
     * )
     *
     */
    private $active;

    /**
     * @OA\Property(
     *     title="role_id",
     *     description="role_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $role_id;

    /**
     * @OA\Property(
     *     title="next_round",
     *     description="next_round",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $next_round;

    /**
     * @OA\Property(
     *     title="circle",
     *     description="circle",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $circle;

    /**
     * @OA\Property(
     *     title="number",
     *     description="number",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $number;

    public function role(): hasOne
    {
        return $this->hasOne( UserAccountRole::class, 'id', 'role_id');
    }

    public function user(): hasOne
    {
        return $this->hasOne( User::class, 'id', 'user_id');
    }

    public function givers(): HasMany
    {
        return $this->hasMany(RoundGiver::class, 'account_id', 'id');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class, 'account_id', 'id');
    }

    public function to(): HasMany
    {
        return $this->hasMany(Report::class, 'to_id', 'id');
    }

    public function from(): HasMany
    {
        return $this->hasMany(Report::class, 'from_id', 'id');
    }

    public function roundType(): hasOne
    {
        return $this->hasOne( RoundType::class, 'id', 'next_round');
    }
}
