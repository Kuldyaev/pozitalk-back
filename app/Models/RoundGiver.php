<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @OA\Schema(
 *     title="RoundGiver",
 *     description="Даритель раунда",
 *     @OA\Xml(
 *         name="RoundGiver"
 *     )
 * )
 */
class RoundGiver extends Model
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
     *     title="status_id",
     *     description="status_id",
     *     format="int64",
     *     example=1
     * )
     *
     */
    private $status_id;

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
     *     title="start",
     *     description="время когда появился даритель",
     *     format="date",
     *     example="2019-05-17"
     * )
     */
    private $start;

    protected $fillable = [
        'round_id',
        'status_id',
        'account_id',
        'start',
        'is_distributed',
        'is_congratulated',
    ];

    public function round(): hasOne
    {
        return $this->hasOne( Round::class, 'id', 'round_id');
    }

    public function account(): hasOne
    {
        return $this->hasOne( UserAccount::class, 'id', 'account_id');
    }

    public function pay(): hasOne
    {
        return $this->hasOne( RoundGiverPay::class, 'giver_id', 'id');
    }
}
