<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 *     title="Round",
 *     description="Раунд (круг)",
 *     @OA\Xml(
 *         name="Round"
 *     )
 * )
 */
class Round extends Model
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
     *     title="account_id",
     *     description="account_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $account_id;

    /**
     * @OA\Property(
     *     title="round_type_id",
     *     description="round_type_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $round_type_id;

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
     *     title="verification_code",
     *     description="verification_code",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $verification_code;

    /**
     * @OA\Property(
     *     title="price",
     *     description="price",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $price;

    /**
     * @OA\Property(
     *     title="created_at",
     *     description="дата создания записи",
     *     format="date",
     *     example="2019-05-17"
     * )
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="updated_at",
     *     description="дата изменения записи",
     *     format="date",
     *     example="2019-05-17"
     * )
     */
    private $updated_at;

    protected $fillable = [
        'account_id',
        'round_type_id',
        'active',
        'verification_code',
        'price',
        'created_at',
        'updated_at',
    ];

    public function type(): hasOne
    {
        return $this->hasOne( RoundType::class, 'id', 'round_type_id');
    }

    public function account(): hasOne
    {
        return $this->hasOne( UserAccount::class, 'id', 'account_id');
    }

    public function roundGivers(): HasMany
    {
        return $this->hasMany(RoundGiver::class, 'round_id', 'id');
    }
}
